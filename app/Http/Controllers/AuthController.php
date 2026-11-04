<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Hash;
use App\Models\Content;

class AuthController extends Controller
{
    // public function index()
    // {
    //     return view('admin.auth.login');
    // }
    public function index()
    {
        // Fetch logo & banner from contents table
        $logo   = Content::ofType('logo');
        $banner = Content::ofType('banner');

        return view('admin.auth.login', compact('logo', 'banner'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        $remember = $request->has('remember_me') ? true : false;
    
        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            $user = Auth::user();
    
            if ($user->role !== 'admin') {
                Auth::logout();
    
                return redirect()->route('admin.login')
                    ->with('message', 'Only admin can login here.')
                    ->with('alert-type', 'error');
            }
    
            return redirect()->route('admin.dashboard');
        }
    
        return redirect()->route('admin.login')
            ->with('message', 'Invalid-Credentials')
            ->with('alert-type', 'error');
    }
    
    public function forgotPassword(Request $request)
    {
        $logo   = Content::ofType('logo');
        $banner = Content::ofType('banner');

        return view('admin.auth.forgot', compact('logo', 'banner'));
        
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            $otp = rand(1000, 9999);

            $user->update([
                'otp' => bcrypt($otp),
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

            Mail::to($user->email)->send(new \App\Mail\SendOtp($otp));

            return response()->json(['success' => true, 'message' => 'OTP sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:4',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            // Check if the OTP matches and is not expired
            if (!Hash::check($request->otp, $user->otp)) {
                return response()->json(['success' => false, 'message' => 'Invalid OTP.']);
            }

            if (Carbon::now()->greaterThan($user->otp_expires_at)) {
                return response()->json(['success' => false, 'message' => 'OTP has expired.']);
            }

            // OTP verified; send reset password form
            $resetPasswordHtml = view('admin.partials.reset-password-form', ['email' => $request->email])->render();

            return response()->json([
                                    'success' => true, 
                                    'message' => 'OTP verified successfully.',
                                    'reset_password_html' => $resetPasswordHtml,
                                ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            $user->update([
                'password' => Hash::make($request->password),
                'otp' => null,
                'otp_expires_at' => null,
            ]);

            return response()->json(['success' => true, 'message' => 'Password reset successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}
