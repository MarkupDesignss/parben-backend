<?php

namespace App\Services;

use App\Models\StripeSetting;

class StripeConfig
{
    /**
     * Cached StripeSetting instance (per-request + cross-request).
     */
    public static function setting(): StripeSetting
    {
        return cache()->rememberForever(
            'stripe_setting',
            fn () => StripeSetting::current()
        );
    }

    /**
     * Current active mode: 'test' or 'live'.
     */
    public static function mode(): string
    {
        return self::setting()->mode ?? 'test';
    }

    public static function isLive(): bool
    {
        return self::mode() === 'live';
    }

    public static function isTest(): bool
    {
        return self::mode() === 'test';
    }

    /**
     * Publishable key for current mode.
     * DB value wins; falls back to config('services.stripe.*').
     */
    public static function publishableKey(): ?string
    {
        $s = self::setting();

        return self::isLive()
            ? ($s->live_publishable_key ?: config('services.stripe.live.key'))
            : ($s->test_publishable_key ?: config('services.stripe.test.key'));
    }

    /**
     * Secret key for current mode.
     * DB value wins; falls back to config('services.stripe.*').
     */
    public static function secretKey(): ?string
    {
        $s = self::setting();

        return self::isLive()
            ? ($s->live_secret_key ?: config('services.stripe.live.secret'))
            : ($s->test_secret_key ?: config('services.stripe.test.secret'));
    }

    /**
     * Webhook signing secret.
     * Pass $mode explicitly for webhook routes (test/live URLs).
     */
    public static function webhookSecret(?string $mode = null): ?string
    {
        $s    = self::setting();
        $mode = $mode ?? self::mode();

        return $mode === 'live'
            ? ($s->live_webhook_secret ?: config('services.stripe.live.webhook_secret'))
            : ($s->test_webhook_secret ?: config('services.stripe.test.webhook_secret'));
    }

    /**
     * Call after saving settings so the next request gets fresh values.
     */
    public static function flush(): void
    {
        cache()->forget('stripe_setting');
    }
}