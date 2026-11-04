<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RouterCaseController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HelpController;
use App\Http\Controllers\Api\InvestigatorAuthController;
use App\Http\Controllers\Api\TokenPurchaseController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\RouterLogController;
use App\Http\Controllers\Api\RouterDeviceController;
use App\Http\Controllers\Api\RouterBrandController;
use App\Http\Controllers\Api\UnsupportedDeviceController;

Route::get('storage', function(){
    Artisan::call('storage:link');
});

Route::post('/contact', [ContactController::class, 'store']);

Route::any('/send-message', [ContactController::class, 'store']);

Route::post('/investigator/signup', [InvestigatorAuthController::class, 'signup']);

Route::get('login', function(){
    return response()->json(['success' => false, 'message' => 'Invalid token!!']);
})->name('login');

Route::post('/cases2', [RouterCaseController::class, 'store2']);

Route::get('/pages', [PageController::class, 'index']);

Route::get('/pages/{slug}', [PageController::class, 'getPage']);


Route::post('/help', [HelpController::class, 'store']);
Route::get('/contents', [ContentController::class, 'apiIndex']);

// Public
Route::post('/investigator/login', [InvestigatorAuthController::class, 'login']);

// Public webhook (no auth)
Route::post('stripe/webhook', [StripeWebhookController::class, 'handle'])->name('api.stripe.webhook');

// Redirect endpoints (optional – for Stripe to return after payment)
Route::get('stripe/success', [TokenPurchaseController::class, 'success'])->name('api.stripe.success');
Route::get('stripe/cancel', [TokenPurchaseController::class, 'cancel'])->name('api.stripe.cancel');

 // Password reset routes
Route::post('/investigator/forgot-password', [InvestigatorAuthController::class, 'forgotPassword']);
Route::post('/investigator/verify-otp', [InvestigatorAuthController::class, 'verifyOtp']);
Route::post('/investigator/reset-password', [InvestigatorAuthController::class, 'resetPassword']);
Route::post('/investigator/resend-otp', [InvestigatorAuthController::class, 'resendOtp']);

Route::get('brands', [RouterBrandController::class, 'index']);
Route::post('brands', [RouterBrandController::class, 'store']);
Route::get('brands/{id}', [RouterBrandController::class, 'show']);
Route::put('brands/{id}', [RouterBrandController::class, 'update']);
Route::delete('brands/{id}', [RouterBrandController::class, 'destroy']);

// Protected
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/investigator/me', [InvestigatorAuthController::class, 'me']);
    Route::post('/investigator/edit-profile', [InvestigatorAuthController::class, 'editProfile']);
    Route::delete('/investigator/delete-account', [InvestigatorAuthController::class, 'deleteAccount']);

    Route::post('/cases', [RouterCaseController::class, 'store']);
    Route::get('/cases', [RouterCaseController::class, 'cases']);
    
    Route::post('/router-logs', [RouterLogController::class, 'store']);
    Route::get('/router-logs', [RouterLogController::class, 'show']); 
    Route::get('/router-logs/check-case', [RouterLogController::class, 'checkCaseId']);
    Route::get('/router-logs/{id}', [RouterLogController::class, 'show']);
    Route::get('/router-logs/{id}/download-pdf', [RouterLogController::class, 'downloadPdf']);
    
    Route::get('investigator/token-packs', [TokenPurchaseController::class, 'getPacks']);
    Route::post('investigator/purchase-tokens', [TokenPurchaseController::class, 'purchase']);
    Route::get('investigator/token-balance', [TokenPurchaseController::class, 'balance']);
    
     Route::prefix('router')->group(function () {
        
        // Main CRUD endpoints
        Route::get('devices', [RouterDeviceController::class, 'index']);           // GET all devices
        Route::post('devices', [RouterDeviceController::class, 'store']);          // POST create devices
        Route::get('devices/{identifier}', [RouterDeviceController::class, 'show']); // GET single device
        Route::put('devices/{id}', [RouterDeviceController::class, 'update']);      // PUT update device
        Route::patch('devices/{id}', [RouterDeviceController::class, 'update']);    // PATCH update device
        Route::get('devices/by-brand/{routerType?}', [RouterDeviceController::class, 'getDevicesByBrand']);
        Route::delete('devices/{identifier}', [RouterDeviceController::class, 'destroy']); // DELETE device
        Route::get('devices/by-router/{routerId}', [RouterDeviceController::class, 'getDevicesByRouter']);
        
        // Special endpoints
        Route::patch('devices/{identifier}/block', [RouterDeviceController::class, 'toggleBlock']); // Block/Unblock
        Route::get('devices/stats/{routerId}', [RouterDeviceController::class, 'getRouterStats']); // Router stats
        Route::get('devices/types/all', [RouterDeviceController::class, 'getDeviceTypes']); // Get all device types
        Route::get('devices/routers/list', [RouterDeviceController::class, 'getRouters']); // Get all routers
        
        // Unsupported device report
        Route::post('unsupported-devices', [UnsupportedDeviceController::class, 'store']);
        
    });

});