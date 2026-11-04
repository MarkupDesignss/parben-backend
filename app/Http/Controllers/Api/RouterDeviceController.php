<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RouterDevice;
use App\Models\RouterBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class RouterDeviceController extends Controller
{
    /**
     * POST /api/router/devices - Store router devices
     */
    // public function store(Request $request)
    // {
    //     Log::info('=== ROUTER DEVICE SAVE START ===', $request->all());

    //     $validator = Validator::make($request->all(), [
    //         'routerId' => 'required|string|max:255',
    //         'routerName' => 'required|string|max:255',
    //         'routerType' => 'required|string|max:255',
    //         'devices' => 'required|array|min:1',
    //         'devices.*.deviceName' => 'required|string|max:255',
    //         'devices.*.macAddress' => 'required|string|max:255',
    //         'devices.*.ipAddress' => 'required|ip',
    //         'devices.*.deviceType' => 'required|string|max:255',
    //         'devices.*.band' => 'required|string|max:255',
    //         'devices.*.txRate' => 'required|integer|min:0',
    //         'devices.*.rxRate' => 'required|integer|min:0',
    //         'devices.*.duration' => 'required|string|max:255',
    //         'devices.*.isBlocked' => 'sometimes|boolean'
    //     ]);

    //     if ($validator->fails()) {
    //         Log::error('Validation failed', $validator->errors()->toArray());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation error',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $validated = $validator->validated();

    //     $userId = auth()->id();
    //     if (!$userId) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthenticated'
    //         ], 401);
    //     }

    //     $savedDevices = [];
    //     $errors = [];

    //     foreach ($validated['devices'] as $device) {
    //         try {
    //             $deviceData = [
    //                 'user_id' => $userId,
    //                 'router_id' => $validated['routerId'],
    //                 'router_name' => $validated['routerName'],
    //                 'router_type' => $validated['routerType'],
    //                 'device_name' => $device['deviceName'],
    //                 'mac_address' => $device['macAddress'],
    //                 'ip_address' => $device['ipAddress'],
    //                 'device_type' => $device['deviceType'],
    //                 'band' => $device['band'],
    //                 'tx_rate' => $device['txRate'],
    //                 'rx_rate' => $device['rxRate'],
    //                 'duration' => $device['duration'],
    //                 'is_blocked' => $device['isBlocked'] ?? false,
    //             ];

    //             $newDevice = RouterDevice::create($deviceData);
    //             $savedDevices[] = $newDevice;
                
    //             Log::info('New device created', [
    //                 'id' => $newDevice->id,
    //                 'mac' => $device['macAddress'],
    //                 'created_at' => $newDevice->created_at
    //             ]);
                
    //         } catch (\Exception $e) {
    //             $errors[] = [
    //                 'mac_address' => $device['macAddress'],
    //                 'error' => $e->getMessage()
    //             ];
    //             Log::error('Failed to save device', [
    //                 'mac' => $device['macAddress'],
    //                 'error' => $e->getMessage()
    //             ]);
    //         }
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => count($errors) > 0 
    //             ? 'Devices saved with some errors' 
    //             : 'Devices saved successfully',
    //         'data' => [
    //             'saved_devices' => $savedDevices,
    //             'errors' => $errors,
    //             'total_processed' => count($validated['devices']),
    //             'total_saved' => count($savedDevices)
    //         ]
    //     ], count($errors) > 0 ? 207 : 201);
    // }
    
    public function store(Request $request)
    {
        Log::info('=== ROUTER DEVICE SAVE START ===', $request->all());
    
        // 1. VALIDATION RULES
        $validator = Validator::make($request->all(), [
            // UPDATED: Ensures 'routerId' is a valid primary key from the router_brands table
            'routerId' => 'required|string|exists:router_brands,default_router_id',
            'routerName' => 'required|string|max:255',
            'routerType' => 'required|string|max:255',
            'devices' => 'required|array|min:1',
            'devices.*.deviceName' => 'required|string|max:255',
            'devices.*.macAddress' => [
                'required',
                'string',
                'max:255',
                // OPTIONAL: Prevents duplicate MAC addresses for the same authenticated user
                //Rule::unique('router_devices', 'mac_address')->where('user_id', auth()->id()),
            ],
            'devices.*.ipAddress' => 'required|ip',
            'devices.*.deviceType' => 'required|string|max:255',
            'devices.*.band' => 'required|string|max:255',
            'devices.*.txRate' => 'required|integer|min:0',
            'devices.*.rxRate' => 'required|integer|min:0',
            'devices.*.duration' => 'required|string|max:255',
            'devices.*.isBlocked' => 'sometimes|boolean'
        ]);
    
        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $validated = $validator->validated();
    
        // FIX: Convert RouterBrand 'id' (e.g., 8) to 'default_router_id' (e.g., "ASUS_001")
        $routerBrand = RouterBrand::find($validated['routerId']);
        $finalRouterId = $routerBrand ? $routerBrand->default_router_id : $validated['routerId'];
    
        // 2. AUTHENTICATION CHECK
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
    
        $savedDevices = [];
        $errors = [];
    
        // 3. PROCESS EACH DEVICE
        foreach ($validated['devices'] as $device) {
            try {
                $deviceData = [
                    'user_id' => $user->id,
                    'router_id' => $finalRouterId,
                    'router_name' => $validated['routerName'],
                    'router_type' => $validated['routerType'],
                    'device_name' => $device['deviceName'],
                    'mac_address' => $device['macAddress'],
                    'ip_address' => $device['ipAddress'],
                    'device_type' => $device['deviceType'],
                    'band' => $device['band'],
                    'tx_rate' => $device['txRate'],
                    'rx_rate' => $device['rxRate'],
                    'duration' => $device['duration'],
                    'is_blocked' => $device['isBlocked'] ?? false,
                ];
    
                $newDevice = RouterDevice::create($deviceData);
                $savedDevices[] = $newDevice;
    
                Log::info('New device created', [
                    'id' => $newDevice->id,
                    'mac' => $device['macAddress'],
                    'created_at' => $newDevice->created_at
                ]);
    
            } catch (\Exception $e) {
                $errors[] = [
                    'mac_address' => $device['macAddress'],
                    'error' => $e->getMessage()
                ];
                Log::error('Failed to save device', [
                    'mac' => $device['macAddress'],
                    'error' => $e->getMessage()
                ]);
            }
        }
    
        // 4. FINAL RESPONSE
        return response()->json([
            'success' => count($errors) === 0, // true only if zero errors
            'message' => count($errors) > 0
                ? 'Devices saved with some errors'
                : 'Devices saved successfully',
            'data' => [
                'saved_devices' => $savedDevices,
                'errors' => $errors,
                'total_processed' => count($validated['devices']),
                'total_saved' => count($savedDevices)
            ]
        ], count($errors) > 0 ? 207 : 201);
    }
    

    /**
     * GET /api/router/devices - Get all router devices
     */
    public function index(Request $request)
    {
        Log::info('=== ROUTER DEVICE FETCH START ===', $request->all());

        $userId = auth()->id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $query = RouterDevice::where('user_id', $userId);

        // Filter by router_id
        if ($request->has('routerId') && !empty($request->routerId)) {
            $query->forRouter($request->routerId);
        }

        // Filter by is_blocked
        if ($request->has('isBlocked') && $request->isBlocked !== '') {
            $isBlocked = filter_var($request->isBlocked, FILTER_VALIDATE_BOOLEAN);
            $query->blocked($isBlocked);
        }

        // Search by device name, MAC, or IP
        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        // Filter by device_type
        if ($request->has('deviceType') && !empty($request->deviceType)) {
            $query->where('device_type', $request->deviceType);
        }

        // Filter by band
        if ($request->has('band') && !empty($request->band)) {
            $query->where('band', $request->band);
        }

        // Order by
        $orderBy = $request->orderBy ?? 'id';
        $orderDirection = $request->orderDirection ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        // Pagination
        $perPage = $request->has('perPage') ? (int) $request->perPage : null;
        $devices = $perPage ? $query->paginate($perPage) : $query->get();

        // Get statistics
        $stats = [
            'total_devices' => RouterDevice::where('user_id', $userId)->count(),
            'blocked_devices' => RouterDevice::where('user_id', $userId)->where('is_blocked', true)->count(),
            'unique_routers' => RouterDevice::where('user_id', $userId)->distinct('router_id')->count(),
            'devices_by_type' => RouterDevice::where('user_id', $userId)
                ->select('device_type', \DB::raw('count(*) as count'))
                ->groupBy('device_type')
                ->get()
                ->pluck('count', 'device_type')
                ->toArray(),
            'devices_by_band' => RouterDevice::where('user_id', $userId)
                ->select('band', \DB::raw('count(*) as count'))
                ->groupBy('band')
                ->get()
                ->pluck('count', 'band')
                ->toArray()
        ];

        Log::info('=== ROUTER DEVICE FETCH END ===', [
            'total_fetched' => $devices instanceof \Illuminate\Pagination\LengthAwarePaginator 
                ? $devices->total() 
                : $devices->count(),
            'filters' => $request->all()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Devices retrieved successfully',
            'data' => $devices,
            'stats' => $stats
        ]);
    }

    /**
     * GET /api/router/devices/{identifier} - Get a specific device
     */
    public function show($identifier)
    {
        Log::info('=== ROUTER DEVICE SHOW START ===', ['identifier' => $identifier]);

        $userId = auth()->id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Find by ID or MAC address
        $device = is_numeric($identifier)
            ? RouterDevice::where('id', $identifier)->where('user_id', $userId)->first()
            : RouterDevice::where('mac_address', $identifier)->where('user_id', $userId)->first();

        if (!$device) {
            Log::error('Device not found', ['identifier' => $identifier, 'user_id' => $userId]);
            return response()->json([
                'success' => false,
                'message' => 'Device not found'
            ], 404);
        }

        Log::info('=== ROUTER DEVICE SHOW END ===', ['device_id' => $device->id]);

        return response()->json([
            'success' => true,
            'message' => 'Device retrieved successfully',
            'data' => $device
        ]);
    }

    /**
     * GET /api/router/devices/by-router/{routerId} - Get devices by router ID
     */
     public function getDevicesByRouter($routerId)
    {
        Log::info('=== GET DEVICES BY ROUTER START ===', ['routerId' => $routerId]);
    
        $userId = auth()->id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }
    
        Log::info('Fetching devices for user', [
            'user_id' => $userId,
            'router_id' => $routerId
        ]);
    
        $devices = RouterDevice::where('user_id', $userId)
            ->where('router_id', $routerId)
            ->orderBy('device_name')
            ->get();
    
        // NEW: Check if no devices found
        if ($devices->isEmpty()) {
            Log::info('No devices found for router', ['routerId' => $routerId]);
            return response()->json([
                'success' => false,
                'message' => 'No devices found for this router ID',
                'router_id' => $routerId
            ], 404);
        }
    
        Log::info('Devices found', [
            'count' => $devices->count(),
            'device_ids' => $devices->pluck('id')->toArray(),
            'device_names' => $devices->pluck('device_name')->toArray()
        ]);
    
        foreach ($devices as $index => $device) {
            Log::info("Device {$index}", $device->toArray());
        }
    
        $routerInfo = $devices->first();
        $routerName = $routerInfo ? $routerInfo->router_name : null;
    
        $stats = [
            'router_id' => $routerId,
            'router_name' => $routerName,
            'total_devices' => $devices->count(),
            'blocked_devices' => $devices->where('is_blocked', true)->count(),
            'active_devices' => $devices->where('is_blocked', false)->count(),
            'devices_by_type' => $devices->groupBy('device_type')->map->count()->toArray(),
            'devices_by_band' => $devices->groupBy('band')->map->count()->toArray()
        ];
    
        Log::info('=== GET DEVICES BY ROUTER END ===', [
            'routerId' => $routerId,
            'total' => $devices->count(),
            'stats' => $stats
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Devices retrieved successfully for router',
            'data' => [
                'router_id' => $routerId,
                'router_name' => $routerName,
                'devices' => $devices,
                'stats' => $stats
            ]
        ]);
    }

    /**
     * PUT/PATCH /api/router/devices/{id} - Update a device
     */
    public function update(Request $request, $id)
    {
        Log::info('=== ROUTER DEVICE UPDATE START ===', ['id' => $id]);

        $userId = auth()->id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $device = RouterDevice::where('id', $id)->where('user_id', $userId)->first();
        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'deviceName' => 'sometimes|string|max:255',
            'ipAddress' => 'sometimes|ip',
            'deviceType' => 'sometimes|string|max:255',
            'band' => 'sometimes|string|max:255',
            'txRate' => 'sometimes|integer|min:0',
            'rxRate' => 'sometimes|integer|min:0',
            'duration' => 'sometimes|string|max:255',
            'isBlocked' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Map request fields to model fields
        $updateData = [];
        if (isset($validated['deviceName'])) $updateData['device_name'] = $validated['deviceName'];
        if (isset($validated['ipAddress'])) $updateData['ip_address'] = $validated['ipAddress'];
        if (isset($validated['deviceType'])) $updateData['device_type'] = $validated['deviceType'];
        if (isset($validated['band'])) $updateData['band'] = $validated['band'];
        if (isset($validated['txRate'])) $updateData['tx_rate'] = $validated['txRate'];
        if (isset($validated['rxRate'])) $updateData['rx_rate'] = $validated['rxRate'];
        if (isset($validated['duration'])) $updateData['duration'] = $validated['duration'];
        if (isset($validated['isBlocked'])) $updateData['is_blocked'] = $validated['isBlocked'];

        $device->update($updateData);

        Log::info('=== ROUTER DEVICE UPDATE END ===', ['device_id' => $device->id]);

        return response()->json([
            'success' => true,
            'message' => 'Device updated successfully',
            'data' => $device
        ]);
    }

    /**
     * PATCH /api/router/devices/{identifier}/block - Block/Unblock a device
     */
    public function toggleBlock(Request $request, $identifier)
    {
        Log::info('=== ROUTER DEVICE TOGGLE BLOCK START ===', ['identifier' => $identifier]);

        $validator = Validator::make($request->all(), [
            'isBlocked' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = auth()->id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Find by ID or MAC address
        $device = is_numeric($identifier)
            ? RouterDevice::where('id', $identifier)->where('user_id', $userId)->first()
            : RouterDevice::where('mac_address', $identifier)->where('user_id', $userId)->first();

        if (!$device) {
            Log::error('Device not found', ['identifier' => $identifier]);
            return response()->json([
                'success' => false,
                'message' => 'Device not found'
            ], 404);
        }

        $device->update([
            'is_blocked' => $request->isBlocked
        ]);

        Log::info('=== ROUTER DEVICE TOGGLE BLOCK END ===', [
            'device_id' => $device->id,
            'is_blocked' => $request->isBlocked
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->isBlocked 
                ? 'Device blocked successfully' 
                : 'Device unblocked successfully',
            'data' => $device
        ]);
    }

    /**
     * DELETE /api/router/devices/{identifier} - Delete a device
     */
    public function destroy($identifier)
    {
        Log::info('=== ROUTER DEVICE DELETE START ===', ['identifier' => $identifier]);

        $userId = auth()->id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // Find by ID or MAC address
        $device = is_numeric($identifier)
            ? RouterDevice::where('id', $identifier)->where('user_id', $userId)->first()
            : RouterDevice::where('mac_address', $identifier)->where('user_id', $userId)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device not found'
            ], 404);
        }

        $device->delete();

        Log::info('=== ROUTER DEVICE DELETE END ===', ['deleted_id' => $identifier]);

        return response()->json([
            'success' => true,
            'message' => 'Device deleted successfully'
        ]);
    }

    /**
     * GET /api/router/devices/stats/{routerId} - Get router statistics
     */
    public function getRouterStats($routerId)
    {
        Log::info('=== ROUTER STATS START ===', ['routerId' => $routerId]);

        $userId = auth()->id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $devices = RouterDevice::where('user_id', $userId)
            ->where('router_id', $routerId)
            ->get();

        $stats = [
            'router_id' => $routerId,
            'total_devices' => $devices->count(),
            'blocked_devices' => $devices->where('is_blocked', true)->count(),
            'devices_by_type' => $devices->groupBy('device_type')->map->count()->toArray(),
            'devices_by_band' => $devices->groupBy('band')->map->count()->toArray(),
            'devices' => $devices->map(function ($device) {
                return [
                    'device_name' => $device->device_name,
                    'mac_address' => $device->mac_address,
                    'ip_address' => $device->ip_address,
                    'device_type' => $device->device_type,
                    'band' => $device->band,
                    'is_blocked' => $device->is_blocked
                ];
            })
        ];

        Log::info('=== ROUTER STATS END ===');

        return response()->json([
            'success' => true,
            'message' => 'Router statistics retrieved successfully',
            'data' => $stats
        ]);
    }

    /**
     * GET /api/router/devices/types - Get all device types
     */
    public function getDeviceTypes()
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $types = RouterDevice::where('user_id', $userId)
            ->distinct('device_type')
            ->pluck('device_type')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /**
     * GET /api/router/devices/routers - Get all router IDs
     */
    public function getRouters()
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $routers = RouterDevice::where('user_id', $userId)
            ->distinct('router_id')
            ->select('router_id', 'router_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $routers
        ]);
    }
    
    public function getDevicesByBrand(Request $request, $routerType = null)
    {
        $routerType = $routerType ?? $request->query('router_type');
        
        $user = auth()->user();
        
        $devices = RouterDevice::where('user_id', $user->id)
                    ->where('router_type', $routerType)
                    ->get();
        
        return response()->json($devices);
    }
    
    
}