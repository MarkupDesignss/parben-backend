<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StripeSetting;
use App\Services\StripeConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeSettingController extends Controller
{
    public function edit()
    {
        $setting = StripeSetting::current();
        return view('admin.stripe.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mode'                 => 'required|in:test,live',
            'test_publishable_key' => 'nullable|string|max:255',
            'test_secret_key'      => 'nullable|string|max:255',
            'test_webhook_secret'  => 'nullable|string|max:255',
            'live_publishable_key' => 'nullable|string|max:255',
            'live_secret_key'      => 'nullable|string|max:255',
            'live_webhook_secret'  => 'nullable|string|max:255',
        ]);

        // Live mode sirf super-admin enable kar sake
        if ($data['mode'] === 'live' && !auth()->user()->isSuperAdmin()) {
            return back()->withErrors('Only super admin can switch to Live mode.');
        }

        // Basic format validation
        foreach ([
            'test_publishable_key' => 'pk_test_',
            'test_secret_key'      => 'sk_test_',
            'live_publishable_key' => 'pk_live_',
            'live_secret_key'      => 'sk_live_',
        ] as $field => $prefix) {
            if (!empty($data[$field]) && !str_starts_with($data[$field], $prefix)) {
                return back()->withErrors("$field must start with $prefix");
            }
        }
        foreach (['test_webhook_secret', 'live_webhook_secret'] as $field) {
            if (!empty($data[$field]) && !str_starts_with($data[$field], 'whsec_')) {
                return back()->withErrors("$field must start with whsec_");
            }
        }

        // Optional: Stripe key validity check
        $secretToVerify = $data['mode'] === 'live'
            ? ($data['live_secret_key'] ?? null)
            : ($data['test_secret_key'] ?? null);

        if ($secretToVerify) {
            $resp = Http::withToken($secretToVerify)
                ->acceptJson()
                ->get('https://api.stripe.com/v1/balance');

            if ($resp->failed()) {
                return back()->withErrors('Stripe key invalid lag rahi hai. Please verify.');
            }
        }

        // Empty field = purani key preserve
        $setting = StripeSetting::current();
        foreach ($data as $k => $v) {
            if ($k === 'mode') {
                $setting->$k = $v;
            } elseif ($v !== null && $v !== '') {
                $setting->$k = $v;
            }
        }
        $setting->save();

        StripeConfig::flush();

        // Audit log (Spatie Activity Log agar installed hai; warna simple Log)
        Log::info('Stripe settings updated', [
            'mode'  => $data['mode'],
            'by'    => auth()->user()->email,
        ]);

        return back()->withSuccess('Stripe settings updated successfully.');
    }
}