<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::where('is_active', 1)
                     ->latest()
                     ->paginate(10);
    
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.form');
    }

    public function store(Request $request)
    {
        try{
            $data = $request->validate([
                'title' => 'required',
                'slug' => 'required|unique:pages,slug',
                'content' => 'nullable',
                'image' => 'nullable|image',
                'desc_image' => 'nullable|image', // NEW FIELD
                'partner_images.*' => 'nullable|image',
                'partner_cards' => 'nullable|array',
                'partner_cards.*.image' => 'nullable|file|image',
                'partner_cards.*.title' => 'nullable|string',
                'partner_cards.*.description' => 'nullable|string',
            ]);

            // Handle main image
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('pages', 'public');
            }

            // Handle banner image (NEW)
            if ($request->hasFile('desc_image')) {
                $data['desc_image'] = $request->file('desc_image')->store('banners', 'public');
            }

            // Handle partner images
            if ($request->hasFile('partner_images')) {
                $partnerImages = [];
                foreach ($request->file('partner_images') as $image) {
                    $partnerImages[] = $image->store('partners', 'public');
                }
                $data['partner_images'] = $partnerImages;
            }

            $partnerCards = [];
            if ($request->has('partner_cards')) {
                foreach ($request->partner_cards as $card) {
                    $cardData = [
                        'title' => $card['title'] ?? '',
                        'description' => $card['description'] ?? '',
                        'image' => null,
                    ];

                    if (isset($card['image']) && $card['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $cardData['image'] = $card['image']->store('partner_cards', 'public');
                    }

                    $partnerCards[] = $cardData;
                }

                $data['partner_cards'] = $partnerCards;
            }

            Page::create($data);

            return redirect()->route('admin.pages.index')->with('success', 'Page created!');
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title' => 'required',
            'slug' => 'required|unique:pages,slug,' . $page->id,
            'content' => 'nullable',
            'image' => 'nullable|image',
            'desc_image' => 'nullable|image', // NEW FIELD
            'partner_images.*' => 'nullable|image',
            'partner_cards' => 'nullable|array',
            'partner_cards.*.image' => 'nullable|file|image',
            'partner_cards.*.title' => 'nullable|string',
            'partner_cards.*.description' => 'nullable|string',
            // Removal flags
            'remove_image' => 'nullable|in:0,1',
            'remove_desc_image' => 'nullable|in:0,1',
            'remove_partner_images' => 'nullable|array',
            'remove_partner_images.*' => 'nullable|integer',
            'remove_partner_card_images' => 'nullable|array',
            'remove_partner_card_images.*' => 'nullable|integer',
        ]);

        // ============================
        // Handle MAIN image
        // ============================
        if ($request->input('remove_image') == '1' && $page->image) {
            Storage::disk('public')->delete($page->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            // Delete old image if it wasn't already removed
            if ($page->image && $request->input('remove_image') != '1') {
                Storage::disk('public')->delete($page->image);
            }
            $data['image'] = $request->file('image')->store('pages', 'public');
        }

        // ============================
        // Handle DESC (banner) image
        // ============================
        if ($request->input('remove_desc_image') == '1' && $page->desc_image) {
            Storage::disk('public')->delete($page->desc_image);
            $data['desc_image'] = null;
        }

        if ($request->hasFile('desc_image')) {
            if ($page->desc_image && $request->input('remove_desc_image') != '1') {
                Storage::disk('public')->delete($page->desc_image);
            }
            $data['desc_image'] = $request->file('desc_image')->store('banners', 'public');
        }

        // ============================
        // Handle PARTNER images (array)
        // ============================
        $existingPartnerImages = $page->partner_images ?? [];
        $removePartnerIndexes = $request->input('remove_partner_images', []);

        foreach ($removePartnerIndexes as $index) {
            if (isset($existingPartnerImages[$index])) {
                Storage::disk('public')->delete($existingPartnerImages[$index]);
                unset($existingPartnerImages[$index]);
            }
        }
        $existingPartnerImages = array_values($existingPartnerImages); // reindex

        if ($request->hasFile('partner_images')) {
            foreach ($request->file('partner_images') as $image) {
                $existingPartnerImages[] = $image->store('partners', 'public');
            }
        }

        $data['partner_images'] = $existingPartnerImages;

        // ============================
        // Handle PARTNER CARDS
        // ============================
        $existingCards = $page->partner_cards ?? [];
        $removeCardImageIndexes = $request->input('remove_partner_card_images', []);

        // Delete removed card images from storage
        foreach ($removeCardImageIndexes as $index) {
            if (isset($existingCards[$index]['image']) && $existingCards[$index]['image']) {
                Storage::disk('public')->delete($existingCards[$index]['image']);
                $existingCards[$index]['image'] = null;
            }
        }

        $partnerCards = [];
        if ($request->has('partner_cards')) {
            foreach ($request->partner_cards as $index => $card) {
                $existingImage = $existingCards[$index]['image'] ?? null;

                $cardData = [
                    'title' => $card['title'] ?? '',
                    'description' => $card['description'] ?? '',
                    'image' => $existingImage,
                ];

                if (isset($card['image']) && $card['image'] instanceof \Illuminate\Http\UploadedFile) {
                    if ($existingImage) {
                        Storage::disk('public')->delete($existingImage);
                    }
                    $cardData['image'] = $card['image']->store('partner_cards', 'public');
                }

                $partnerCards[] = $cardData;
            }

            $data['partner_cards'] = $partnerCards;
        } else {
            // No partner_cards in request — preserve existing (with image removals applied)
            $data['partner_cards'] = array_values($existingCards);
        }

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated!');
    }

    public function destroy(Page $page)
    {
        // Clean up stored files
        if ($page->image) {
            Storage::disk('public')->delete($page->image);
        }
        if ($page->desc_image) {
            Storage::disk('public')->delete($page->desc_image);
        }
        if (!empty($page->partner_images)) {
            foreach ($page->partner_images as $img) {
                Storage::disk('public')->delete($img);
            }
        }
        if (!empty($page->partner_cards)) {
            foreach ($page->partner_cards as $card) {
                if (!empty($card['image'])) {
                    Storage::disk('public')->delete($card['image']);
                }
            }
        }

        $page->delete();
        return back()->with('success', 'Page deleted!');
    }

    public function toggleStatus($id)
    {
        $page = Page::findOrFail($id);
        $page->is_active = !$page->is_active;
        $page->save();

        return back()->with('success', 'Status updated!');
    }

    public function getPage($slug)
    {
        try{
            $page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
            return response()->json([
                'success' => true, 
                'data' => [
                    'title' => $page->title,
                    'content' => $page->content,
                    'image' => $page->image,
                    'desc_image' => $page->desc_image, // NEW FIELD
                    'partner_images' => $page->partner_images,
                    'partner_cards' => $page->partner_cards, 
                ],
                'base_url' => url('/') . '/storage/'
            ]);
        }catch(\Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }
}