<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Investigator;
use App\Models\RouterDevice;
use App\Models\RouterLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class InvestigatorAuthController extends Controller
{
    // Investigator Signup
    public function signup(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'organization' => 'nullable|string',
            'email' => 'required|email|unique:investigators',
            'phone' => 'nullable|string',
            'password' => 'required|min:6|confirmed',
        ]);

        $investigator = Investigator::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'organization' => $request->organization,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $token = $investigator->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Signup successful',
            'token' => $token,
            'user' => $investigator,
        ]);
    }

    // Investigator Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $investigator = User::where('email', $request->email)->where('role', 'investigator')->first();

        if (!$investigator || !Hash::check($request->password, $investigator->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $token = $investigator->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $investigator,
            'base_url' => url('/') . '/storage/'
        ]);
    }

    // Get logged-in investigator
    public function me(Request $request)
    {
        return response()->json(['success' => true, 'user' => $request->user(), 'base_url' => url('/') . '/storage/']);
    }

    // Edit profile
    public function editProfile(Request $request)
    {
        try {
            $user = $request->user();

            $request->validate([
                'first_name' => 'sometimes|string',
                'last_name' => 'sometimes|string',
                'organization' => 'sometimes|nullable|string',
                'phone' => [
                    'sometimes',
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^[0-9+\-\s\(\)]+$/',
                    'min:10'
                ],
                'password' => 'sometimes|nullable|min:6|confirmed',
                'image' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $data = [
                'name' => ($request->first_name ?? $user->first_name) .' '. ($request->last_name ?? $user->last_name),
                'organization' => $request->organization ?? $user->organization,
                'phone' => $request->phone ?? $user->phone,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ];

            if ($request->hasFile('image')) {
                if ($user->image && Storage::exists('profile_images/'.$user->image)) {
                    Storage::delete('profile_images/'.$user->image);
                }

                $file = $request->file('image');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->storeAs('profile_images', $filename, 'public');
                $data['image'] = 'profile_images/'.$filename;
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user,
                'base_url' => url('/') . '/storage/'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send password reset OTP to email (Mobile App)
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            // Check if user is an investigator
            $user = User::where('email', $request->email)->where('role', 'investigator')->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No investigator found with this email address.'
                ], 404);
            }

            // Generate 6-digit OTP
            $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Delete old tokens
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            // Store new token
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]);

            // Send email with OTP
            Mail::send('emails.mobile-password-reset', [
                'user' => $user,
                'otp' => $token,
                'email' => $request->email
            ], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Password Reset OTP');
            });

            return response()->json([
                'success' => true,
                'message' => 'OTP has been sent to your email.',
                'otp' => $token // Remove in production, only for testing
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No reset request found. Please request OTP again.'
            ], 400);
        }

        // Check if token is expired (60 minutes)
        $tokenCreatedAt = Carbon::parse($resetRecord->created_at);
        if ($tokenCreatedAt->diffInMinutes(Carbon::now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.'
            ], 400);
        }

        // Verify OTP
        if (!Hash::check($request->otp, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'email' => $request->email
        ]);
    }

    /**
     * Reset password using OTP
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            // Check if user is an investigator
            $user = User::where('email', $request->email)->where('role', 'investigator')->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No investigator found with this email address.'
                ], 404);
            }

            // Verify OTP
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (!$resetRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP.'
                ], 400);
            }

            // Check if OTP is expired (60 minutes)
            $tokenCreatedAt = Carbon::parse($resetRecord->created_at);
            if ($tokenCreatedAt->diffInMinutes(Carbon::now()) > 60) {
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new one.'
                ], 400);
            }

            // Verify OTP
            if (!Hash::check($request->otp, $resetRecord->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP.'
                ], 400);
            }

            // Update password
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the OTP token
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            // Revoke all existing tokens (forces re-login)
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully. Please login with your new password.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            $user = User::where('email', $request->email)->where('role', 'investigator')->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No investigator found with this email address.'
                ], 404);
            }

            // Generate new OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Update OTP
            DB::table('password_reset_tokens')->where('email', $request->email)->update([
                'token' => Hash::make($otp),
                'created_at' => Carbon::now()
            ]);

            // Send email with new OTP
            Mail::send('emails.mobile-password-reset', [
                'user' => $user,
                'otp' => $otp,
                'email' => $request->email
            ], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('New Password Reset OTP');
            });

            return response()->json([
                'success' => true,
                'message' => 'New OTP has been sent to your email.',
                'otp' => $otp // Remove in production
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Permanently delete the investigator account and all associated data.
     */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();
    
        // 1. Validate password (security check)
        $request->validate([
            'password' => 'required|string',
        ]);
    
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password. Please try again.'
            ], 401);
        }
    
        // 2. Start database transaction
        DB::beginTransaction();
    
        try {
            // Count records before deletion (for the response message)
            $investigatorCount = User::where('email', $user->email)->count();
            $deviceCount = RouterDevice::where('user_id', $user->id)->count();
            $logCount = RouterLog::where('user_id', $user->id)->count();
    
            // 3. Delete physical log files (if any)
            $logFiles = RouterLog::where('user_id', $user->id)
                                 ->whereNotNull('file_path')
                                 ->pluck('file_path');
            foreach ($logFiles as $filePath) {
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }
    
            // 4. Delete from router_logs
            RouterLog::where('user_id', $user->id)->delete();
    
            // 5. Delete from router_devices
            RouterDevice::where('user_id', $user->id)->delete();
    
            // 6. Delete investigator record (if exists)
            User::where('email', $user->email)->delete();
    
            // 7. Revoke all access tokens
            $user->tokens()->delete();
    
            // 8. Finally, delete the user account
            $user->delete();
    
            // Commit transaction
            DB::commit();
    
            // Build a clear success message
            $message = "Account permanently deleted.";
    
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted' => [
                    'user' => true,
                    'investigator' => $investigatorCount,
                    'devices' => $deviceCount,
                    'logs' => $logCount,
                    'tokens' => true,
                ]
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }
}