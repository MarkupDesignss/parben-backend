<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RouterCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class RouterCaseController extends Controller
{
    public function store(Request $request)
    {
        try{
            $request->validate([
                'case_id' => 'required|unique:router_cases,case_id',
                'agent_first_name' => 'required|string|max:100',
                'agent_last_name' => 'required|string|max:100',
                'organization' => 'required|string|max:100',
                'zip_file' => 'required|file|mimes:zip',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Store ZIP file in public disk
            $zipPath = $request->file('zip_file')->store('cases', 'public');

            // Store image if present
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('cases/images', 'public');
            }

            // Store files in DigitalOcean Spaces
            // $zipPath = $request->file('zip_file')->store('cases', 'spaces');

            // $imagePath = null;
            // if ($request->hasFile('image')) {
            //     $imagePath = $request->file('image')->store('cases/images', 'spaces');
            // }

            $investigator = $request->user();

            // Save in DB
            $case = RouterCase::create([
                'investigator_id' => $investigator->id,
                'case_id' => $request->case_id,
                'agent_first_name' => $investigator->name,
                'agent_last_name' => '',
                'organization' => $request->organization,
                'zip_path' => $zipPath,
                'router_image' => $imagePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Case uploaded successfully.',
                'data' => $case
            ]);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    
    public function store2(Request $request)
    {
        $request->validate([
            'case_id' => 'required|string',
            'agent_first_name' => 'required|string',
            'agent_last_name' => 'required|string',
            'router_image' => 'nullable|image|max:5120', // max 5MB

            'admin_ip' => 'required|ip',
            'username' => 'required|string',
            'password' => 'required|string',
            'ssid_id' => 'nullable|string',
        ]);

        // Save image
        $routerImagePath = null;
        if ($request->hasFile('router_image')) {
            $routerImagePath = $request->file('router_image')->store('router_images', 'public');
        }

        // Step 1: Login to router
        // $baseUrl = 'https://192.168.1.1';
        // $username = 'admin';
        // $password = 'admin';
        
        $baseUrl = "http://{$request->admin_ip}";
        $username = $request->username;
        $password = $request->password;
        $ssidId = $request->ssid_id ?? 1;
        
        $token = null;

        $loginResponse = Http::withOptions(['verify' => false])
            ->put("$baseUrl/api/usersession/", [
                'login' => $username,
                'password' => $password,
                'logout' => false
            ]);

        if ($loginResponse->successful()) {
            $token = $loginResponse->json('sessionToken');
        } else {
            return response()->json(['error' => 'Router login failed'], 401);
        }

        // Step 2: Collect logs
        $headers = ['Cookie' => "session=$token; username=$username"];
        $options = ['verify' => false];

        $deviceInfo = Http::withOptions($options)->withHeaders($headers)->get("$baseUrl/api/deviceinfo")->json();
        $hosts = Http::withOptions($options)->withHeaders($headers)->get("$baseUrl/api/lans/1/hosts/")->json();
        $ssids = Http::withOptions($options)->withHeaders($headers)->get("$baseUrl/api/landev/wifiradios/1/ssids/")->json();

        $timestamp = now()->format('Ymd_His');
        $jsonFileName = "router_logs_$timestamp.json";
        $jsonPath = "router_logs/$jsonFileName";
        $logData = [
            'fetched_at' => now()->toDateTimeString(),
            'device_info' => $deviceInfo,
            'hosts' => $hosts,
            'ssids' => $ssids,
        ];
        Storage::disk('public')->put($jsonPath, json_encode($logData, JSON_PRETTY_PRINT));
        $jsonFullPath = storage_path("app/public/$jsonPath");

        // Step 3: Generate hash
        $hash = hash_file('sha256', $jsonFullPath);
        $hashFileName = "router_logs_$timestamp.hash.txt";
        $hashPath = "router_logs/$hashFileName";
        Storage::disk('public')->put($hashPath, "SHA-256: $hash");
        $hashFullPath = storage_path("app/public/$hashPath");

        // Step 4: Package ZIP
        $zipFileName = "case_$timestamp.zip";
        $zipRelPath = "router_packages/$zipFileName";
        $zipFullPath = storage_path("app/public/$zipRelPath");

        if (!file_exists(dirname($zipFullPath))) {
            mkdir(dirname($zipFullPath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE) === true) {
            $zip->addFile($jsonFullPath, $jsonFileName);
            $zip->addFile($hashFullPath, $hashFileName);
            if ($routerImagePath) {
                $zip->addFile(storage_path("app/public/$routerImagePath"), basename($routerImagePath));
            }
            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create ZIP'], 500);
        }

        // Step 5: Save case
        $case = RouterCase::create([
            'case_id' => $request->case_id,
            'agent_first_name' => $request->agent_first_name,
            'agent_last_name' => $request->agent_last_name,
            'agent_middle_name' => $request->agent_middle_name,
            'organization' => $request->organization,
            'router_image' => $routerImagePath,
            'zip_path' => $zipRelPath,
        ]);

        return response()->json([
            'message' => 'Case logged successfully',
            'case' => $case,
            'zip_download_url' => asset("storage/$zipRelPath"),
        ]);
    }

    public function cases(Request $request)
    {
        try
        {

            $cases = RouterCase::where('investigator_id', $request->user()->id)->latest()->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $cases
            ]);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
