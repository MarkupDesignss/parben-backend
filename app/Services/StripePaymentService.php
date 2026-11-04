<?php

namespace App\Services;

use Stripe\StripeClient;
use Stripe\Checkout\Session;
use App\Models\TokenPack;

class StripePaymentService
{
    protected $stripe;

    public function __construct()
    {
        //$this->stripe = new StripeClient(config('services.stripe.secret'));
        $this->stripe = new StripeClient(\App\Services\StripeConfig::secretKey());
    }

    public function createCheckoutSession(TokenPack $pack, $investigatorId)
    {
        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $pack->name . ' - ' . $pack->token_count . ' Tokens',
                    ],
                    'unit_amount' => $pack->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('api.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('api.stripe.cancel'),
            'metadata' => [
                'investigator_id' => $investigatorId,
                'pack_id' => $pack->id,
                'token_count' => $pack->token_count,
            ],
        ]);

        return $session;
    }

    public function retrieveSession($sessionId)
    {
        return $this->stripe->checkout->sessions->retrieve($sessionId);
    }
}