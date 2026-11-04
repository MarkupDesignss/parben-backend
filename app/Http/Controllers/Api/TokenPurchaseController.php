<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TokenPack;
use App\Services\StripePaymentService;
use App\Services\TokenService;
use Illuminate\Http\Request;

class TokenPurchaseController extends Controller
{
    public function getPacks()
    {
        $packs = TokenPack::where('is_active', true)->get();
        return response()->json(['success' => true, 'data' => $packs]);
    }

    public function purchase(Request $request, StripePaymentService $stripeService)
    {
        $request->validate(['pack_id' => 'required|exists:token_packs,id']);
        $pack = TokenPack::findOrFail($request->pack_id);

        $session = $stripeService->createCheckoutSession($pack, auth()->id());

        return response()->json([
            'success' => true,
            'checkout_url' => $session->url,
            'session_id' => $session->id,
        ]);
    }

    public function balance()
    {
        $balance = TokenService::getBalance(auth()->id());
        return response()->json(['success' => true, 'balance' => $balance]);
    }

    // Optional – for redirect after payment (webhook does actual credit)
    public function success()
    {
        return response()->json(['success' => true, 'message' => 'Payment successful. Tokens will be credited shortly.']);
    }

    public function cancel()
    {
        return response()->json(['success' => false, 'message' => 'Payment cancelled.']);
    }
}