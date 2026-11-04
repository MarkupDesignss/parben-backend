<?php

namespace App\Services;

use App\Models\Token;
use App\Models\TokenTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TokenService
{
    /**
     * Get the current token balance of a user.
     */
    public static function getBalance(int $userId): int
    {
        $token = Token::where('investigator_id', $userId)->first();
        return $token ? (int) $token->balance : 0;
    }

    /**
     * Add tokens to a user's balance and log the transaction.
     *
     * @param int   $userId
     * @param int   $amount
     * @param array $meta  Optional: ['pack_id', 'reference', 'description', 'session_id']
     */
    public static function addTokens(int $userId, int $amount, array $meta = []): void
    {
        DB::transaction(function () use ($userId, $amount, $meta) {
            // 1. Update or create token balance
            $token = Token::where('investigator_id', $userId)->first();

            if ($token) {
                $token->increment('balance', $amount);
                $newBalance = $token->balance;
            } else {
                $token = Token::create([
                    'investigator_id' => $userId,
                    'balance'         => $amount,
                ]);
                $newBalance = $amount;
            }

            // 2. Insert transaction record
            TokenTransaction::create([
                'investigator_id' => $userId,
                'pack_id'         => $meta['pack_id'] ?? null,
                'amount'          => $amount,
                'type'            => 'purchase',
                'reference'       => $meta['reference'] ?? $meta['session_id'] ?? null, // fallback to session_id
                'description'     => $meta['description'] ?? "Added {$amount} tokens",
                'balance_after'   => $newBalance,
            ]);

            Log::info("Tokens added: user={$userId}, amount={$amount}, newBalance={$newBalance}");
        });
    }

    /**
     * Deduct tokens (optional – you can also log deductions).
    */
 
    public static function deductTokens(int $userId, int $amount, array $meta = []): bool
    {
        $token = Token::where('investigator_id', $userId)->first();
    
        if ($token && $token->balance >= $amount) {
            DB::transaction(function () use ($token, $userId, $amount, $meta) {
                $token->decrement('balance', $amount);
                $newBalance = $token->balance;
    
                TokenTransaction::create([
                    'investigator_id' => $userId,
                    'pack_id'         => null,
                    'amount'          => -$amount,
                    'type'            => 'download',
                    'reference'       => $meta['reference'] ?? null,
                    'description'     => $meta['description'] ?? "Deducted {$amount} tokens",
                    'balance_after'   => $newBalance,
                ]);
            });
            return true;
        }
    
        return false;
    }
}