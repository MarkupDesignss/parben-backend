<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class ContentController extends Controller
{
    /**
     * List all contents (Read).
     */
    public function index()
    {
        $contents = Content::latest()->paginate(10);

        return view('admin.contents.index', compact('contents'));
    }
    
   public function apiIndex()
    {
        try {
            $contents = Content::where('type', 'logo')
                ->latest()
                ->paginate(10);
    
            $contents->getCollection()->transform(function ($content) {
                $content->image_url = $content->image
                    ? asset('storage/' . $content->image)
                    : null;
    
                return $content;
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Logo contents fetched successfully.',
                'data' => $contents,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch logo contents.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.contents.create', [
            'types' => Content::TYPES,
        ]);
    }

    /**
     * Store new content (Create).
     */
    public function store(Request $request)
    {
        $validated = $this->validateContent($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('contents', 'public');
        }

        Content::create($validated);

        return redirect()
            ->route('admin.contents.index')
            ->with('success', 'Content created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Content $content)
    {
        return view('admin.contents.edit', [
            'content' => $content,
            'types'   => Content::TYPES,
        ]);
    }

    /**
     * Update content (Update).
     */
    public function update(Request $request, Content $content)
    {
        $validated = $this->validateContent($request, $content->id);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($content->image && Storage::disk('public')->exists($content->image)) {
                Storage::disk('public')->delete($content->image);
            }
            $validated['image'] = $request->file('image')->store('contents', 'public');
        }

        $content->update($validated);

        return redirect()
            ->route('admin.contents.index')
            ->with('success', 'Content updated successfully.');
    }

    /**
     * Shared validation rules.
     */
    protected function validateContent(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'type'  => ['required', Rule::in(array_keys(Content::TYPES))],
            'title' => ['nullable', 'string', 'max:255'],
            'text'  => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg'],
        ]);
    }
}
