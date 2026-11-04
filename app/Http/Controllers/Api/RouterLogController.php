<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RouterLog;
use App\Models\RouterBrand;
use App\Services\TokenService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Dompdf\Dompdf;
use Dompdf\Options;

class RouterLogController extends Controller
{
    /**
     * Store router log (with hash generation)
    */

    public function store(Request $request)
    {
        Log::info('=== ROUTER LOG SAVE START ===', $request->all());
    
        $validator = Validator::make($request->all(), [
            'caseId'          => 'required|string|min:1|max:255|unique:router_logs,case_id',
            'routerId'        => 'required|string|exists:router_brands,default_router_id',
            'routerModel'     => 'nullable|string|max:255',
            'logGeneratedAt'  => 'nullable|date_format:Y-m-d H:i:s',
            'logContent'      => 'required|string|min:1',
        ]);
    
        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }
    
        $validated = $validator->validated();
    
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    
        $routerBrand = RouterBrand::find($validated['routerId']);
        $finalRouterId = $routerBrand ? $routerBrand->default_router_id : $validated['routerId'];
    
        Log::info('Validation passed', [
            'user_id' => $user->id,
            'caseId'  => $validated['caseId'],
            'routerId'=> $validated['routerId'],
            'finalRouterId' => $finalRouterId,
            'logContentLength' => strlen($validated['logContent'])
        ]);
    
        // ===== GENERATE HASH FROM LOG CONTENT =====
        $contentHash = hash('sha256', $validated['logContent']);
        Log::info('Generated Hash: ' . $contentHash);
    
        // Save log file
        $filename = 'router_' . $finalRouterId . '_' . Str::random(8) . '.log';
        $filePath = 'router_logs/' . $filename;
    
        $disk = Storage::disk('public');
        if (!$disk->exists('router_logs')) {
            $disk->makeDirectory('router_logs');
        }
    
        $saved = $disk->put($filePath, $validated['logContent']);
        if (!$saved) {
            Log::error('Failed to save log file');
            return response()->json(['error' => 'Failed to save log file'], 500);
        }
    
        $fileExists = $disk->exists($filePath);
        $fileSize = $fileExists ? $disk->size($filePath) : 0;
    
        if (!$fileExists) {
            Log::error('File write verification failed');
            return response()->json(['error' => 'File write verification failed'], 500);
        }
    
        // ===== SAVE TO DATABASE WITH CONVERTED router_id =====
        $log = RouterLog::create([
            'user_id'           => $user->id,
            'case_id'           => $validated['caseId'],
            'router_id'         => $finalRouterId,
            'router_model'      => $validated['routerModel'] ?? null,
            'log_generated_at'  => $validated['logGeneratedAt'] ?? now(),
            'log_content'       => $validated['logContent'],
            'hash'              => $contentHash,
            'file_path'         => $filePath,
            'file_size'         => $fileSize,
        ]);
    
        Log::info('Database record created with ID: ' . $log->id);
        Log::info('=== ROUTER LOG SAVE END ===');
    
        return response()->json([
            'success'    => true,
            'message'    => 'Router log saved successfully',
            'log_id'     => $log->id,
            'hash'       => $contentHash,
            'public_url' => asset('storage/' . $filePath)
        ], 201);
    }
    
    
    public function show($id = null)
    {
        if (is_null($id)) {
            $logs = RouterLog::where('user_id', auth()->id())
                             ->orderBy('id', 'desc')
                             ->get();
            return response()->json($this->formatLogs($logs));
        }
        
        $logs = RouterLog::where('user_id', auth()->id())
                         ->where('router_id', $id)   // $id = router_id
                         ->orderBy('id', 'desc')
                         ->get();
    
        return response()->json($this->formatLogs($logs));
    }
    

    private function formatLogs($logs)
    {
        return $logs->map(function ($log) {
            return $this->formatSingleLog($log);
        });
    }
    
    private function formatSingleLog($log)
    {
        $data = $log->toArray();
        if ($log->file_path && Storage::disk('public')->exists($log->file_path)) {
            $data['public_file_url'] = Storage::disk('public')->url($log->file_path);
        } else {
            $data['public_file_url'] = null;
        }
        return $data;
    }

    /**
     * Download log as PDF with SHA-256 Hash - CPanel Compatible
     */
    // public function downloadPdf($id)
    // {
    //     try {
    //         Log::info('=== API: DOWNLOAD PDF STARTED ===');
    //         Log::info('Log ID: ' . $id);
            
    //         $log = RouterLog::where('id', $id)
    //                         ->where('user_id', auth()->id())
    //                         ->firstOrFail();
            
    //         // Get log content from database
    //         $logContent = $log->log_content ?? 'No log content available.';
            
    //         // Generate hash from ORIGINAL log_content
    //         $contentHash = hash('sha256', $logContent);
            
    //         // Store hash in database
    //         $log->hash = $contentHash;
    //         $log->save();
            
    //         Log::info('Content Hash: ' . $contentHash);
            
    //         $verificationCode = substr($contentHash, 0, 8) . '...' . substr($contentHash, -8);
            
    //         $data = [
    //             'log' => $log,
    //             'log_content' => $logContent,
    //             'file_hash' => $contentHash,
    //             'verification_code' => $verificationCode,
    //             'generated_at' => now()->format('d/m/Y H:i:s')
    //         ];
            
    //         // ============================================================
    //         // CPANEL COMPATIBLE - Direct DomPDF (No Facade)
    //         // ============================================================
            
    //         // Set options
    //         $options = new Options();
    //         $options->set('defaultFont', 'DejaVu Sans');
    //         $options->set('isHtml5ParserEnabled', true);
    //         $options->set('isRemoteEnabled', false);
    //         $options->set('chroot', realpath(base_path()));
            
    //         // Create DomPDF instance
    //         $dompdf = new Dompdf($options);
            
    //         // Load HTML from view
    //         $html = view('admin.exports.single_log_pdf', $data)->render();
    //         $dompdf->loadHtml($html);
            
    //         // Set paper size and orientation
    //         $dompdf->setPaper('A4', 'portrait');
            
    //         // Render PDF
    //         $dompdf->render();
            
    //         // Generate filename
    //         $routerId = preg_replace('/[^A-Za-z0-9]/', '_', $log->router_id);
    //         $filename = 'log_' . $routerId . '_' . $log->id . '_' . now()->format('Ymd_His') . '.pdf';
            
    //         Log::info('PDF Generated: ' . $filename);
    //         Log::info('=== API: DOWNLOAD PDF ENDED ===');
            
    //         // ============================================================
    //         // IMPORTANT: API response mein PDF download karna
    //         // ============================================================
            
    //         // Option 1: Stream (download)
    //         return $dompdf->stream($filename, ['Attachment' => true]);
            
    //         // Option 2: If stream doesn't work, use base64 for API
    //         // $pdfContent = $dompdf->output();
    //         // return response()->json([
    //         //     'success' => true,
    //         //     'pdf_base64' => base64_encode($pdfContent),
    //         //     'filename' => $filename
    //         // ]);
            
    //     } catch (\Exception $e) {
    //         Log::error('API: Download PDF error: ' . $e->getMessage());
    //         Log::error($e->getTraceAsString());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error downloading PDF: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function downloadPdf($id)
    {
        try {
            Log::info('=== API: DOWNLOAD PDF STARTED ===');
            Log::info('Log ID: ' . $id);
    
            $log = RouterLog::where('id', $id)
                            ->where('user_id', auth()->id())
                            ->firstOrFail();
    
            // ============================================================
            // TOKEN CHECK & DEDUCTION (1 token per download)
            // ============================================================
            $userId = auth()->id();
            $balance = TokenService::getBalance($userId);
    
            if ($balance < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient tokens. You need 1 token to download this log.'
                ], 402);
            }
    
            $deducted = TokenService::deductTokens($userId, 1, [
                'reference'   => 'download_' . $log->id,
                'description' => "Downloaded log: {$log->router_id} (ID: {$log->id})"
            ]);
    
            if (!$deducted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to deduct token. Please try again.'
                ], 500);
            }
    
            // ============================================================
            // (Optional) If you have a download_histories table, log here:
            // DownloadHistory::create([...]);
            // ============================================================
    
            // --- Existing PDF generation code (unchanged) ---
            $logContent = $log->log_content ?? 'No log content available.';
            $contentHash = hash('sha256', $logContent);
            $log->hash = $contentHash;
            $log->save();
    
            Log::info('Content Hash: ' . $contentHash);
    
            $verificationCode = substr($contentHash, 0, 8) . '...' . substr($contentHash, -8);
    
            $data = [
                'log'              => $log,
                'log_content'      => $logContent,
                'file_hash'        => $contentHash,
                'verification_code'=> $verificationCode,
                'generated_at'     => now()->format('d/m/Y H:i:s')
            ];
    
            // DomPDF options
            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', false);
            $options->set('chroot', realpath(base_path()));
    
            $dompdf = new Dompdf($options);
            $html = view('admin.exports.single_log_pdf', $data)->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
    
            $routerId = preg_replace('/[^A-Za-z0-9]/', '_', $log->router_id);
            $filename = 'log_' . $routerId . '_' . $log->id . '_' . now()->format('Ymd_His') . '.pdf';
    
            Log::info('PDF Generated: ' . $filename);
            Log::info('=== API: DOWNLOAD PDF ENDED ===');
    
            return $dompdf->stream($filename, ['Attachment' => true]);
    
        } catch (\Exception $e) {
            Log::error('API: Download PDF error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error downloading PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download log as PDF with Base64 response (for API)
     * Ye method API response mein base64 encoded PDF return karega
     */
    public function downloadPdfBase64($id)
    {
        try {
            Log::info('=== API: DOWNLOAD PDF BASE64 STARTED ===');
            Log::info('Log ID: ' . $id);
            
            $log = RouterLog::where('id', $id)
                            ->where('user_id', auth()->id())
                            ->firstOrFail();
            
            $logContent = $log->log_content ?? 'No log content available.';
            $contentHash = hash('sha256', $logContent);
            
            $log->hash = $contentHash;
            $log->save();
            
            $verificationCode = substr($contentHash, 0, 8) . '...' . substr($contentHash, -8);
            
            $data = [
                'log' => $log,
                'log_content' => $logContent,
                'file_hash' => $contentHash,
                'verification_code' => $verificationCode,
                'generated_at' => now()->format('d/m/Y H:i:s')
            ];
            
            $options = new Options();
            $options->set('defaultFont', 'DejaVu Sans');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', false);
            
            $dompdf = new Dompdf($options);
            $html = view('admin.exports.single_log_pdf', $data)->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            $pdfContent = $dompdf->output();
            
            $routerId = preg_replace('/[^A-Za-z0-9]/', '_', $log->router_id);
            $filename = 'log_' . $routerId . '_' . $log->id . '_' . now()->format('Ymd_His') . '.pdf';
            
            Log::info('=== API: DOWNLOAD PDF BASE64 ENDED ===');
            
            // Return base64 encoded PDF
            return response()->json([
                'success' => true,
                'message' => 'PDF generated successfully',
                'filename' => $filename,
                'pdf_base64' => base64_encode($pdfContent),
                'hash' => $contentHash,
                'log_id' => $log->id
            ]);
            
        } catch (\Exception $e) {
            Log::error('API: Download PDF Base64 error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error downloading PDF: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Check if a caseId already exists
     */
    public function checkCaseId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caseId' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }
    
        $caseId = $request->input('caseId');
        $exists = RouterLog::where('case_id', $caseId)->exists();
    
        return response()->json([
            'success' => true,
            'exists'  => $exists,
            'caseId'  => $caseId,
        ]);
    }
}