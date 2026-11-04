<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function index()
    {
        try {
            $pages = Page::where('is_active', true)->get();
            
            return response()->json([
                'success' => true,
                'data' => $pages->map(function($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'content' => $page->content,
                        // 'image' => $page->image,
                        // 'desc_image' => $page->desc_image,
                        'banner_image' => url('/') . '/storage/' . $page->image,
                        'desc_image' => url('/') . '/storage/' . $page->desc_image,
                        'created_at' => $page->created_at,
                    ];
                }),
                // 'base_url' => url('/') . '/storage/'
            ]);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getPage($slug)
    {
        try{
            $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
            return response()->json([
                'success' => true, 
                'data' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'content' => $page->content,
                    // 'image' => $page->image,
                    'banner_image' => url('/') . '/storage/' . $page->image,
                    'desc_image' => url('/') . '/storage/' . $page->desc_image,
                ],
                // 'base_url' => url('/') . '/storage/'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }
}