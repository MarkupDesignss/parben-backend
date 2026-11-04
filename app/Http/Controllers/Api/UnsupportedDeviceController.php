<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\UnsupportedDeviceMail;
use App\Models\UnsupportedDeviceReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UnsupportedDeviceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // 1. Validate
        $validated = $request->validate([
            'routerBrand' => 'required|string|max:100',
            'routerModel' => 'required|string|max:255',
            'routerId'    => 'required|string|max:255',
            'routerIp'    => ['required', 'string', 'max:45', 'regex:/^(\d{1,3}\.){3}\d{1,3}$/'],

            'deviceInfo'            => 'required|array',
            'deviceInfo.platform'   => 'required|string|in:iOS,Android',
            'deviceInfo.appVersion' => 'required|string|max:50',
            'deviceInfo.osVersion'  => 'required|string|max:50',

            'diagnostics'               => 'required|array',
            'diagnostics.captureFailed' => 'required|boolean',
            'diagnostics.failureReason' => 'nullable|string|max:1000',
            'diagnostics.routerPage'    => 'nullable|string|max:500',
            'diagnostics.timestamp'     => 'nullable|date',
        ]);

        try {
            // 2. Save to database
            $report = UnsupportedDeviceReport::create([
                'user_id'              => auth()->id(),
                'router_brand'         => $validated['routerBrand'],
                'router_model'         => $validated['routerModel'],
                'router_id'            => $validated['routerId'],
                'router_ip'            => $validated['routerIp'],
                'platform'             => $validated['deviceInfo']['platform'],
                'app_version'          => $validated['deviceInfo']['appVersion'],
                'os_version'           => $validated['deviceInfo']['osVersion'],
                'capture_failed'       => $validated['diagnostics']['captureFailed'],
                'failure_reason'       => $validated['diagnostics']['failureReason'] ?? null,
                'router_page'          => $validated['diagnostics']['routerPage'] ?? null,
                'diagnostic_timestamp' => $validated['diagnostics']['timestamp'] ?? null,
                'status'               => 'pending',
                'email_sent'           => false,
            ]);

            // 3. Send HTML email using Mailable
            $this->sendSupportEmail($report);

            return response()->json([
                'success' => true,
                'message' => 'Report submitted successfully. Support team will review it soon.',
                'data'    => [
                    'reportId' => $report->id,
                    'status'   => $report->status,
                ],
            ], 201);

        } catch (\Throwable $e) {
            Log::error('Unsupported device report failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report. Please try again.',
            ], 500);
        }
    }

    /**
     * UPDATED — Mailable
     */
    private function sendSupportEmail(UnsupportedDeviceReport $report): void
    {
        $supportEmail = env('MAIL_SUPPORT_EMAIL', 'forensics@paraben.com');

        try {
            Mail::to($supportEmail)
                ->send(new UnsupportedDeviceMail($report));

            // Mark email as sent
            $report->update([
                'email_sent'    => true,
                'email_sent_at' => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Support email failed', [
                'report_id' => $report->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function index(): JsonResponse
    {
        $reports = UnsupportedDeviceReport::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $reports,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $report = UnsupportedDeviceReport::with('user:id,name,email')->find($id);

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $report,
        ]);
    }
}