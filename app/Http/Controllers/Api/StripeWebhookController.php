<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TokenService;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, TokenService $tokenService)
    {
        Log::info('Webhook received');

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook signature failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $metadata = $session->metadata;

            $investigatorId = $metadata->investigator_id ?? null;
            $packId = $metadata->pack_id ?? null;
            $tokenCount = $metadata->token_count ?? 0;

            if ($investigatorId && $tokenCount > 0) {
                $tokenService->addTokens(
                    (int)$investigatorId,
                    (int)$tokenCount,
                    [
                        'reference'   => $session->id,
                        'pack_id'     => $packId,
                        'description' => "Purchased {$tokenCount} tokens via Stripe",
                    ]
                );
                Log::info("Tokens added for user {$investigatorId}: +{$tokenCount}");
            } else {
                Log::warning('Missing metadata in webhook', ['metadata' => $metadata]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}