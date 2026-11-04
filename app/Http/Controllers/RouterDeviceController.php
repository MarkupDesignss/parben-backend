<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RouterDevice;
use App\Models\User;
use Illuminate\Http\Request;

class RouterDeviceController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = RouterDevice::with('user')->latest();
        
    //     // Search
    //     if ($request->filled('search')) {
    //         $search = $request->search;
    //         $query->where(function($q) use ($search) {
    //             $q->where('mac_address', 'like', '%' . $search . '%')
    //               ->orWhere('device_name', 'like', '%' . $search . '%')
    //               ->orWhere('router_id', 'like', '%' . $search . '%')
    //               ->orWhere('ip_address', 'like', '%' . $search . '%');
    //         });
    //     }
        
    //     // Filters
    //     if ($request->filled('from_date')) {
    //         $query->whereDate('created_at', '>=', $request->from_date);
    //     }
    //     if ($request->filled('to_date')) {
    //         $query->whereDate('created_at', '<=', $request->to_date);
    //     }
    //     if ($request->filled('device_type')) {
    //         $query->where('device_type', $request->device_type);
    //     }
    //     if ($request->filled('band')) {
    //         $query->where('band', $request->band);
    //     }
    //     if ($request->filled('is_blocked') && $request->is_blocked !== '') {
    //         $query->where('is_blocked', $request->is_blocked);
    //     }
        
    //     $devices = $query->paginate(20);
    //     $deviceTypes = RouterDevice::distinct()->pluck('device_type');
        
    //     return view('admin.router-devices.index', compact('devices', 'deviceTypes'));
    // }
    
      public function index(Request $request)
    {
        $query = RouterDevice::with('user')->latest();
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('mac_address', 'like', '%' . $search . '%')
                  ->orWhere('device_name', 'like', '%' . $search . '%')
                  ->orWhere('router_id', 'like', '%' . $search . '%')
                  ->orWhere('ip_address', 'like', '%' . $search . '%');
            });
        }
        
        // Filters
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }
        if ($request->filled('band')) {
            $query->where('band', $request->band);
        }
        if ($request->filled('is_blocked') && $request->is_blocked !== '') {
            $query->where('is_blocked', $request->is_blocked);
        }
        
        // Get all devices (remove paginate)
        $devices = $query->get();
        
        // Group by date (Y-m-d)
        $groupedDevices = $devices->groupBy(function ($item) {
            return $item->created_at->toDateString(); // e.g., "2026-07-24"
        });
        
        // For filter dropdowns
        $deviceTypes = RouterDevice::distinct()->pluck('device_type');
        
        return view('admin.router-devices.index', compact('groupedDevices', 'deviceTypes'));
    }
    
    public function destroy($id)
    {
        try {
            $device = RouterDevice::findOrFail($id);
            $device->delete();
            
            // Redirect back with success message
            return redirect()->route('admin.router-devices.index')
                ->with('success', 'Device deleted successfully');
                
        } catch (\Exception $e) {
            // Redirect back with error message
            return redirect()->route('admin.router-devices.index')
                ->with('error', 'Error deleting device: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->ids;
            if (empty($ids)) {
                return redirect()->route('admin.router-devices.index')
                    ->with('error', 'No devices selected');
            }
            
            $count = RouterDevice::whereIn('id', $ids)->delete();
            
            return redirect()->route('admin.router-devices.index')
                ->with('success', $count . ' devices deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()->route('admin.router-devices.index')
                ->with('error', 'Error deleting devices: ' . $e->getMessage());
        }
    }

}