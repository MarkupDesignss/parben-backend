<?php

// app/Models/StripeSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeSetting extends Model
{
    protected $fillable = [
        'mode',
        'test_publishable_key', 'test_secret_key', 'test_webhook_secret',
        'live_publishable_key', 'live_secret_key', 'live_webhook_secret',
    ];

    // Secret keys
    protected $casts = [
        'test_secret_key'      => 'encrypted',
        'test_webhook_secret'  => 'encrypted',
        'live_secret_key'      => 'encrypted',
        'live_webhook_secret'  => 'encrypted',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], ['mode' => 'test']);
    }
}