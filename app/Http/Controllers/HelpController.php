<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Help;

class HelpController extends Controller
{
    // Show all helps
    public function index()
    {
        $helps = Help::latest()->paginate(10);
        return view('admin.help', compact('helps'));
    }

    // Show single help
    public function show($id)
    {
        $help = Help::findOrFail($id);
        return view('admin.help.show', compact('help'));
    }

    // Update status
    // public function updateStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required|in:pending,in_progress,resolved,closed'
    //     ]);

    //     $help = Help::findOrFail($id);
    //     $help->status = $request->status;
    //     $help->save();

    //     if ($request->ajax()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Status updated successfully!',
    //             'status' => $help->status,
    //             'badge' => $help->status_badge
    //         ]);
    //     }

    //     return redirect()->route('admin.help')->with('success', 'Status updated successfully.');
    // }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,closed'
        ]);

        $help = Help::findOrFail($id);
        $currentStatus = $help->status;
        $newStatus = $request->status;

        // Define the status flow (forward only)
        $statusFlow = [
            'pending' => ['in_progress', 'resolved', 'closed'],
            'in_progress' => ['resolved', 'closed'],
            'resolved' => ['closed'],
            'closed' => [] // No further changes allowed
        ];

        // Check if the new status is allowed from current status
        if (!in_array($newStatus, $statusFlow[$currentStatus])) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Status cannot be changed backward or to an invalid state. Current status: ' . 
                                ucfirst(str_replace('_', ' ', $currentStatus))
                ], 422);
            }

            return redirect()->back()->with('error', 'Status cannot be changed backward or to an invalid state.');
        }

        // Update status
        $help->status = $newStatus;
        $help->save();

        // Define badge colors
        $badgeColors = [
            'pending' => 'warning',
            'in_progress' => 'primary',
            'resolved' => 'success',
            'closed' => 'secondary'
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated to ' . ucfirst(str_replace('_', ' ', $newStatus)) . ' successfully!',
                'status' => $help->status,
                'badge' => $badgeColors[$help->status] ?? 'secondary'
            ]);
        }

        return redirect()->route('admin.help')->with('success', 'Status updated successfully.');
    }

   // Bulk delete helps
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|string'
        ]);

        $ids = explode(',', $request->ids);
        
        // Get all helps with attachments
        $helps = Help::whereIn('id', $ids)->get();
        
        // Delete attachments
        foreach ($helps as $help) {
            if ($help->attachment && file_exists(storage_path('app/public/' . $help->attachment))) {
                unlink(storage_path('app/public/' . $help->attachment));
            }
        }
        
        // Delete the records
        Help::whereIn('id', $ids)->delete();

        return redirect()->route('admin.helps.index')->with('success', count($ids) . ' help request(s) deleted successfully.');
    }

    // Delete single help
    public function destroy($id)
    {
        $help = Help::findOrFail($id);
        
        // Delete attachment if exists
        if ($help->attachment && file_exists(storage_path('app/public/' . $help->attachment))) {
            unlink(storage_path('app/public/' . $help->attachment));
        }
        
        $help->delete();

        return redirect()->route('admin.helps.index')->with('success', 'Help request deleted successfully.');
    }
}