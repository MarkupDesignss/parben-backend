<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone' => 'nullable',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);
    
        $contact = Contact::create($data);
    
        try {
            $adminEmail = config('mail.admin_email');
            
            if (!$adminEmail) {
                $adminEmail = 'admin@yourdomain.com'; 
            }
    
            Mail::raw("New Contact Message:\n\nFrom: {$data['name']} ({$data['email']})\n\n{$data['message']}", function ($mail) use ($data, $adminEmail) {
                $mail->to($adminEmail)
                     ->subject('New Contact Form Submission');
            });
            
        } catch (\Exception $e) {
            \Log::error('Contact email failed: ' . $e->getMessage());
        }
    
        return response()->json(['success' => true, 'message' => 'Message sent successfully.'], 201);
    }
}
