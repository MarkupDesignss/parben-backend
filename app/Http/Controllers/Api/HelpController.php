<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Help;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\HelpRequestMail;

class HelpController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable',
                'issue' => 'required|string|max:255',
                'message' => 'required|string',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            ]);

            // Handle file upload
            $attachmentPath = null;
            $originalFileName = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $originalFileName = $file->getClientOriginalName();
                $attachmentPath = $file->store('attachments', 'public');
            }

            // Store the Help data
            $help = Help::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'issue' => $validated['issue'],
                'message' => $validated['message'],
                'attachment' => $attachmentPath,
            ]);

            // Send email to admin and kushankrajput16@gmail.com
            $this->sendHelpRequestEmail($help, $attachmentPath, $originalFileName);

            return response()->json([
                'success' => true,
                'message' => 'Help request submitted successfully.',
                'data' => $help
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send email notification for help request
     */
    private function sendHelpRequestEmail($help, $attachmentPath, $originalFileName)
    {
        try {
            // Admin email (you can get this from config or hardcode)
            $adminEmail = config('mail.admin_email', 'admin@yourdomain.com');
            
            
            $recipients = [
                $adminEmail,
                'routersupport@paraben.com'
            ];
            // Send email using Mailable class
            Mail::to($recipients)->send(
                new HelpRequestMail($help, $attachmentPath, $originalFileName)
            );

        } catch (\Exception $e) {
            // Log the error but don't stop the process
            Log::error('Failed to send help request email: ' . $e->getMessage());
        }
    }
}