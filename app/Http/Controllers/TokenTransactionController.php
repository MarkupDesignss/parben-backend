<?php

namespace App\Http\Controllers;

use App\Models\TokenTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class TokenTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = TokenTransaction::with(['investigator', 'pack'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('investigator_id')) {
            $query->where('investigator_id', $request->investigator_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(20);
        $investigators = User::where('role', 'investigator')->get();

        return view('admin.tokens.transactions.index', compact('transactions', 'investigators'));
    }
}