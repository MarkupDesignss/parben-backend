<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RouterBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RouterBrandController extends Controller
{
    /**
     * Helper to convert relative path to full URL
     */
    private function getFullUrl($path)
    {
        if (empty($path)) {
            return null;
        }
        // If path already starts with http, return as is
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        // Otherwise, prepend asset() to generate full URL
        return asset($path);
    }

    /**
     * Transform a single brand model to add full logo URL
     */
    private function transformBrand($brand)
    {
        if ($brand && $brand->logo) {
            $brand->logo = $this->getFullUrl($brand->logo);
        }
        return $brand;
    }

    /**
     * Transform a collection of brands
     */
    private function transformBrands($brands)
    {
        return $brands->map(function ($brand) {
            return $this->transformBrand($brand);
        });
    }

    public function index(Request $request)
    {
        $brands = RouterBrand::where('is_active', true)
                             ->orderBy('name', 'asc') 
                             ->get();
        
        $brands = $this->transformBrands($brands);
        
        return response()->json([
            'status' => 'success',
            'data'   => $brands,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'logo'              => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'website'           => 'nullable|string|max:255',
            'injector'          => 'nullable|string|max:50',
            'description'       => 'nullable|string',
            'color'             => 'nullable|string|max:7',
            'router_type'       => 'nullable|string|max:50',
            'default_router_id' => 'nullable|string|max:50',
            'is_active'         => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = Str::slug($validated['name']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('brands', $filename, 'public');
            $validated['logo'] = Storage::url($path); // stores relative path like /storage/brands/...
        }

        $brand = RouterBrand::create($validated);
        $brand = $this->transformBrand($brand);

        return response()->json([
            'status' => 'success',
            'data'   => $brand,
        ], 201);
    }

    public function show($id)
    {
        $brand = RouterBrand::findOrFail($id);
        $brand = $this->transformBrand($brand);
        return response()->json([
            'status' => 'success',
            'data'   => $brand,
        ]);
    }

    public function update(Request $request, $id)
    {
        $brand = RouterBrand::findOrFail($id);

        $validated = $request->validate([
            'name'              => 'sometimes|string|max:100',
            'logo'              => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'website'           => 'nullable|string|max:255',
            'injector'          => 'nullable|string|max:50',
            'description'       => 'nullable|string',
            'color'             => 'nullable|string|max:7',
            'router_type'       => 'nullable|string|max:50',
            'default_router_id' => 'nullable|string|max:50',
            'is_active'         => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($brand->logo) {
                $oldPath = str_replace('/storage/', '', $brand->logo);
                Storage::disk('public')->delete($oldPath);
            }

            $file = $request->file('logo');
            $filename = Str::slug($brand->name) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('brands', $filename, 'public');
            $validated['logo'] = Storage::url($path);
        }

        $brand->update($validated);
        $brand = $this->transformBrand($brand);

        return response()->json([
            'status' => 'success',
            'data'   => $brand,
        ]);
    }

    public function destroy($id)
    {
        $brand = RouterBrand::findOrFail($id);
        if ($brand->logo) {
            $oldPath = str_replace('/storage/', '', $brand->logo);
            Storage::disk('public')->delete($oldPath);
        }
        $brand->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Brand deleted',
        ]);
    }
}