<?php

namespace App\Http\Controllers;


use App\Models\TokenPack;
use Illuminate\Http\Request;

class TokenPackController extends Controller
{
   public function index()
    {
        $packs = TokenPack::orderBy('price')->paginate(10);
        return view('admin.tokens.packs.index', compact('packs'));
    }

    public function create()
    {
        return view('admin.tokens.packs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'token_count' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        TokenPack::create([
            'name' => $request->name,
            'token_count' => $request->token_count,
            'price' => $request->price,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.tokens.packs.index')
            ->with('success', 'Token pack created successfully.');
    }

    public function edit(TokenPack $pack)
    {
        return view('admin.tokens.packs.edit', compact('pack'));
    }

    public function update(Request $request, TokenPack $pack)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'token_count' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $pack->update([
            'name' => $request->name,
            'token_count' => $request->token_count,
            'price' => $request->price,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.tokens.packs.index')
            ->with('success', 'Token pack updated successfully.');
    }

    public function destroy(TokenPack $pack)
    {
        // Check if any transaction references this pack
        if ($pack->transactions()->count() > 0) {
            return back()->with('error', 'Cannot delete pack because it has associated transactions.');
        }
        $pack->delete();
        return redirect()->route('admin.tokens.packs.index')
            ->with('success', 'Token pack deleted.');
    }
}