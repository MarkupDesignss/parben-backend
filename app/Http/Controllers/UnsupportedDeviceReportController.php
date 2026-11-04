<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UnsupportedDeviceReport;
use Illuminate\Http\Request;

class UnsupportedDeviceReportController extends Controller
{
    /**
     * List all reports with filters.
     */
    public function index(Request $request)
    {
        $query = UnsupportedDeviceReport::with('user:id,name,email');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by router brand
        if ($request->filled('brand')) {
            $query->where('router_brand', 'like', '%' . $request->brand . '%');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('router_brand', 'like', "%{$search}%")
                  ->orWhere('router_model', 'like', "%{$search}%")
                  ->orWhere('router_id', 'like', "%{$search}%")
                  ->orWhere('router_ip', 'like', "%{$search}%");
            });
        }

        $reports = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Stats for dashboard cards
        $stats = [
            'total'    => UnsupportedDeviceReport::count(),
            'pending'  => UnsupportedDeviceReport::where('status', 'pending')->count(),
            'reviewed' => UnsupportedDeviceReport::where('status', 'reviewed')->count(),
            'resolved' => UnsupportedDeviceReport::where('status', 'resolved')->count(),
        ];

        return view('admin.unsupported-devices.index', compact('reports', 'stats'));
    }

    /**
     * Show single report details.
     */
    public function show($id)
    {
        $report = UnsupportedDeviceReport::with('user')->findOrFail($id);
        return view('admin.unsupported-devices.show', compact('report'));
    }

    /**
     * Update report status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved',
        ]);

        $report = UnsupportedDeviceReport::findOrFail($id);
        $report->update(['status' => $request->status]);

        return redirect()->route('admin.unsupported-devices.index')->with('success', 'Status updated successfully.');
    }

    /**
     * Delete single report.
     */
    public function destroy($id)
    {
        $report = UnsupportedDeviceReport::findOrFail($id);
        $report->delete();

        return back()->with('success', 'Report deleted successfully.');
    }

    /**
     * Bulk delete.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:unsupported_device_reports,id',
        ]);
    
        $count = count($request->ids);
        UnsupportedDeviceReport::whereIn('id', $request->ids)->delete();
    
        return response()->json([
            'success' => true,
            'message' => $count . ' reports deleted successfully.',
        ]);
    }

    /**
     * Export reports to CSV.
     */
    public function export(Request $request)
    {
        $reports = UnsupportedDeviceReport::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->get();

        $filename = 'unsupported-device-reports-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($reports) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'User', 'Brand', 'Model', 'Router ID', 'Router IP',
                'Platform', 'App Version', 'OS Version',
                'Capture Failed', 'Failure Reason', 'Router Page',
                'Status', 'Email Sent', 'Created At',
            ]);

            foreach ($reports as $r) {
                fputcsv($file, [
                    $r->id,
                    $r->user->name ?? 'N/A',
                    $r->router_brand,
                    $r->router_model,
                    $r->router_id,
                    $r->router_ip,
                    $r->platform,
                    $r->app_version,
                    $r->os_version,
                    $r->capture_failed ? 'Yes' : 'No',
                    $r->failure_reason,
                    $r->router_page,
                    $r->status,
                    $r->email_sent ? 'Yes' : 'No',
                    $r->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}