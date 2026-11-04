@extends('admin.layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i> Stripe Settings
                    </h4>
                    <div>
                        @if($setting->mode === 'live')
                            <span class="badge bg-danger">LIVE MODE</span>
                        @else
                            <span class="badge bg-success">TEST MODE</span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.stripe.update') }}">
                        @csrf

                        {{-- ==================== MODE SELECTOR ==================== --}}
                        <div class="form-group mb-4">
                            <label class="d-block mb-2">
                                Stripe Mode <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mode"
                                           id="mode_test" value="test"
                                           {{ old('mode', $setting->mode) === 'test' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mode_test">
                                        Test Mode
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mode"
                                           id="mode_live" value="live"
                                           {{ old('mode', $setting->mode) === 'live' ? 'checked' : '' }}
                                           {{ auth()->user()->isSuperAdmin() ? '' : 'disabled' }}>
                                    <label class="form-check-label" for="mode_live">
                                        Live Mode
                                        @unless(auth()->user()->isSuperAdmin())
                                            <small class="text-muted">(super admin only)</small>
                                        @endunless
                                    </label>
                                </div>
                            </div>
                            @error('mode')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror

                            @if($setting->mode === 'live')
                                <div class="alert alert-danger mt-3 mb-0">
                                    <strong>LIVE mode is active.</strong> Real payments will be processed.
                                </div>
                            @endif
                        </div>

                        <hr>

                        {{-- ==================== TEST KEYS ==================== --}}
                        <h5 class="mb-3 text-success">
                             Test Mode Keys
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="test_publishable_key">Publishable Key</label>
                                    <input type="text"
                                           name="test_publishable_key"
                                           id="test_publishable_key"
                                           class="form-control @error('test_publishable_key') is-invalid @enderror"
                                           placeholder="pk_test_..."
                                           value="{{ old('test_publishable_key', $setting->test_publishable_key) }}">
                                    @error('test_publishable_key')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="test_secret_key">
                                        Secret Key
                                        @if($setting->test_secret_key)
                                            <small class="text-success">
                                                set (&hellip;{{ substr($setting->test_secret_key, -4) }})
                                            </small>
                                        @endif
                                    </label>
                                    <input type="password"
                                           name="test_secret_key"
                                           id="test_secret_key"
                                           class="form-control @error('test_secret_key') is-invalid @enderror"
                                           placeholder="sk_test_... (leave blank to keep current)"
                                           autocomplete="new-password">
                                    @error('test_secret_key')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="test_webhook_secret">
                                        Webhook Secret
                                        @if($setting->test_webhook_secret)
                                            <small class="text-success">set</small>
                                        @endif
                                    </label>
                                    <input type="password"
                                           name="test_webhook_secret"
                                           id="test_webhook_secret"
                                           class="form-control @error('test_webhook_secret') is-invalid @enderror"
                                           placeholder="whsec_... (leave blank to keep current)"
                                           autocomplete="new-password">
                                    @error('test_webhook_secret')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- ==================== LIVE KEYS ==================== --}}
                        <h5 class="mb-3 text-danger">
                            Live Mode Keys
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="live_publishable_key">Publishable Key</label>
                                    <input type="text"
                                           name="live_publishable_key"
                                           id="live_publishable_key"
                                           class="form-control @error('live_publishable_key') is-invalid @enderror"
                                           placeholder="pk_live_..."
                                           value="{{ old('live_publishable_key', $setting->live_publishable_key) }}">
                                    @error('live_publishable_key')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="live_secret_key">
                                        Secret Key
                                        @if($setting->live_secret_key)
                                            <small class="text-success">
                                                set (&hellip;{{ substr($setting->live_secret_key, -4) }})
                                            </small>
                                        @endif
                                    </label>
                                    <input type="password"
                                           name="live_secret_key"
                                           id="live_secret_key"
                                           class="form-control @error('live_secret_key') is-invalid @enderror"
                                           placeholder="sk_live_... (leave blank to keep current)"
                                           autocomplete="new-password">
                                    @error('live_secret_key')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="live_webhook_secret">
                                        Webhook Secret
                                        @if($setting->live_webhook_secret)
                                            <small class="text-success">set</small>
                                        @endif
                                    </label>
                                    <input type="password"
                                           name="live_webhook_secret"
                                           id="live_webhook_secret"
                                           class="form-control @error('live_webhook_secret') is-invalid @enderror"
                                           placeholder="whsec_... (leave blank to keep current)"
                                           autocomplete="new-password">
                                    @error('live_webhook_secret')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- ==================== ACTIONS ==================== --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Settings
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Helper note --}}
            <div class="alert alert-info mt-3">
                <strong><i class="fas fa-info-circle me-1"></i> Note:</strong>
                Secret and webhook keys are always masked. Leave a field blank to keep its current value.
            </div>
        </div>
    </div>
</div>
@endsection