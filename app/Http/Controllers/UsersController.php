<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::query()->where('role', '!=', 'admin');

        // Search
        if ($request->filled('search')) {
            $users->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $users->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
            ]);

            Log::info('User creation started', ['email' => $request->email]);

            $user = User::create([
                'name' => $request->name,
                'role' => 'investigator', // ✅ Fixed spelling
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            Log::info('User created successfully', ['user_id' => $user->id, 'role' => $user->role]);

            // ✅ Send email to investigator - Fixed spelling
            if ($user->role == 'investigator') {  // ✅ 'investigator' not 'investicator'
                Log::info('Attempting to send email', ['email' => $user->email]);
                
                try {
                    $loginUrl = route('investigator.login');
                    
                    // ✅ If route doesn't exist, use direct URL
                    // $loginUrl = url('/investigator/login');
                    
                    $emailData = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $request->password,
                        'loginUrl' => $loginUrl
                    ];

                    Mail::send('emails.account_created', $emailData, function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Your Account Has Been Created');
                    });

                    Log::info('Email sent successfully', ['email' => $user->email]);

                } catch (\Exception $e) {
                    Log::error('Email sending failed: ' . $e->getMessage(), [
                        'email' => $user->email,
                        'error' => $e->getMessage()
                    ]);
                    
                    // ✅ Email fail hone par bhi user created successfully redirect ho
                    $notification = [
                        'message' => 'User created successfully but email sending failed.',
                        'alert-type' => 'warning'
                    ];
                    return redirect()->route('admin.users.index')->with($notification);
                }
            } else {
                Log::warning('User role is not investigator', ['role' => $user->role]);
            }

            $notification = [
                'message' => 'User created successfully.',
                'alert-type' => 'success'
            ];
            
            Log::info('Redirecting to users index');
            return redirect()->route('admin.users.index')->with($notification);
            
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('User creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['password', 'password_confirmation'])
            ]);
            
            $notification = [
                'message' => 'Error: ' . $e->getMessage(),
                'alert-type' => 'error'
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        try {
            $roles = Role::all();
            return view('admin.users.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            Log::error('Edit user error: ' . $e->getMessage());
            
            $notification = [
                'message' => $e->getMessage(),
                'alert-type' => 'error'
            ];
            return redirect()->route('admin.users.index')->with($notification);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:6|confirmed',
            ]);

            Log::info('User update started', ['user_id' => $user->id, 'email' => $user->email]);

            $updateData = [
                'name' => $request->name,
                'role' => 'investigator', // ✅ Fixed spelling
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            $user->update($updateData);

            Log::info('User updated successfully', ['user_id' => $user->id]);

            // ✅ Send email if password is updated
            if ($request->filled('password') && $user->role == 'investigator') {
                Log::info('Sending password update email', ['email' => $user->email]);
                
                try {
                    $loginUrl = route('investigator.login');
                    $emailData = [
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $request->password,
                        'loginUrl' => $loginUrl
                    ];

                    Mail::send('emails.account_created', $emailData, function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject('Your Password Has Been Updated');
                    });

                    Log::info('Password update email sent successfully');

                } catch (\Exception $e) {
                    Log::error('Password update email failed: ' . $e->getMessage());
                }
            }

            $notification = [
                'message' => 'User updated successfully.',
                'alert-type' => 'success'
            ];
            return redirect()->route('admin.users.index')->with($notification);
            
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('User update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            $notification = [
                'message' => 'Error: ' . $e->getMessage(),
                'alert-type' => 'error'
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            Log::info('User deletion started', ['user_id' => $user->id, 'email' => $user->email]);
            
            $user->delete();

            Log::info('User deleted successfully', ['user_id' => $user->id]);

            $notification = [
                'message' => 'User deleted successfully.',
                'alert-type' => 'success'
            ];
            return redirect()->route('admin.users.index')->with($notification);
            
        } catch (\Exception $e) {
            Log::error('User deletion failed: ' . $e->getMessage());
            
            $notification = [
                'message' => $e->getMessage(),
                'alert-type' => 'error'
            ];
            return redirect()->route('admin.users.index')->with($notification);
        }
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        try {
            $query = User::query()->where('role', '!=', 'admin');

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            }

            $users = $query->get();

            $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=$filename",
            ];

            $callback = function () use ($users) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for Excel compatibility
                fputs($file, "\xEF\xBB\xBF");
                
                // Headers
                fputcsv($file, ['Name', 'Email', 'Role', 'Status', 'Created At']);

                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->name,
                        $user->email,
                        $user->role,
                        $user->status ?? 'Active',
                        $user->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            Log::info('Users exported', ['count' => $users->count()]);

            return response()->stream($callback, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            
            $notification = [
                'message' => 'Export failed: ' . $e->getMessage(),
                'alert-type' => 'error'
            ];
            return redirect()->route('admin.users.index')->with($notification);
        }
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->ids;
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No users selected'
                ]);
            }

            Log::info('Bulk delete started', ['user_ids' => $ids]);
    
            $count = User::whereIn('id', $ids)->delete();
    
            Log::info('Bulk delete completed', ['deleted_count' => $count]);
    
            return response()->json([
                'success' => true,
                'message' => $count . ' users deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Bulk delete failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting users: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Toggle user status (Active/Inactive)
     */
    public function toggleStatus(User $user)
    {
        try {
            $user->status = $user->status == 'active' ? 'inactive' : 'active';
            $user->save();

            Log::info('User status toggled', [
                'user_id' => $user->id,
                'new_status' => $user->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully.',
                'status' => $user->status
            ]);
            
        } catch (\Exception $e) {
            Log::error('Toggle status failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ]);
        }
    }
}