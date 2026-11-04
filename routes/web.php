<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AdminAuthMiddleware;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\RouterDeviceController;
use App\Http\Middleware\RestrictInvestigator;
use App\Http\Controllers\TokenPackController;
use App\Http\Controllers\TokenTransactionController;
use App\Http\Controllers\UnsupportedDeviceReportController;
use App\Http\Controllers\StripeSettingController;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear-all', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return 'All caches cleared!';
});

Route::get('storage', function(){
    Artisan::call('storage:link');
});

Route::get('/fix-hashes', function() {
    $logs = \App\Models\RouterLog::all();
    $updated = 0;
    foreach ($logs as $log) {
        $newHash = hash('sha256', $log->log_content);
        if ($log->hash !== $newHash) {
            $log->hash = $newHash;
            $log->save();
            $updated++;
        }
    }
    return "✅ $updated logs updated out of " . $logs->count();
});

Route::get('/investigator/login', [AuthController::class, 'index'])->name('investigator.login');

Route::prefix('admin')->name('admin.')->group(function () {
    // Public login routes
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot');
    Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send.otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');

    // Protected routes – requires authentication
    Route::group(['middleware' => [AdminAuthMiddleware::class]], function(){
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/cases', [AdminController::class, 'cases'])->name('cases.index');
        Route::get('/cases/export', [AdminController::class, 'export'])->name('cases.export');
        Route::get('/logs/{id}/export-pdf', [AdminController::class, 'exportSingleLog'])->name('logs.export-pdf');
        Route::post('/logs/export-bulk-pdf', [AdminController::class, 'exportBulkLogs'])->name('logs.export-bulk-pdf');
        Route::post('/verify-pdf-hash', [AdminController::class, 'verifyHash'])->name('verify.hash');
        
        Route::delete('/cases/{id}', [AdminController::class, 'destroy'])->name('cases.destroy');
        Route::post('/cases/bulk-delete', [AdminController::class, 'bulkDelete'])->name('cases.bulk-delete');

        Route::resource('router-devices', RouterDeviceController::class);
        Route::post('router-devices/bulk-delete', [RouterDeviceController::class, 'bulkDelete'])->name('router-devices.bulk-delete');
        
        Route::get('/stripe', [StripeSettingController::class, 'edit'])->name('stripe.edit');
        Route::post('/stripe', [StripeSettingController::class, 'update'])->name('stripe.update');

        // Routes restricted to admins only (investigators are blocked)
        Route::group(['middleware' => [RestrictInvestigator::class]], function(){
            // Users
            Route::get('/users', [UsersController::class, 'index'])->name('users.index');
            Route::post('/users/bulk-delete', [UsersController::class, 'bulkDelete'])->name('users.bulk-delete');
            Route::get('/users/export', [UsersController::class, 'export'])->name('users.export');
            Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
            Route::post('/users', [UsersController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

            // Pages
            Route::get('/pages', [PageController::class, 'index'])->name('pages.index');
            Route::get('/pages/create', [PageController::class, 'create'])->name('pages.create');
            Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
            Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
            Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
            Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
            Route::post('ckeditor/upload', [PageController::class, 'upload'])->name('ckeditor.upload');

            // Contacts
            Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
            Route::post('contacts/bulk-delete', [ContactController::class, 'bulkDelete'])->name('contacts.bulk-delete');
            Route::get('contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');
            Route::delete('contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

            // Help
            Route::get('helps', [HelpController::class, 'index'])->name('helps.index');
            Route::get('helps/{help}', [HelpController::class, 'show'])->name('helps.show');
            Route::delete('helps/{help}', [HelpController::class, 'destroy'])->name('helps.destroy');
            
            Route::get('helps', [HelpController::class, 'index'])->name('helps.index');
            Route::patch('/help/{id}/status', [HelpController::class, 'updateStatus'])->name('help.updateStatus');
            Route::get('helps/{help}', [HelpController::class, 'show'])->name('helps.show');
            Route::delete('helps/{help}', [HelpController::class, 'destroy'])->name('helps.destroy');
            Route::delete('/help/bulk/destroy', [HelpController::class, 'bulkDestroy'])->name('help.bulkDestroy');
            
            // Token Management
            Route::prefix('tokens')->name('tokens.')->group(function () {
                Route::resource('packs', TokenPackController::class)->except(['show']);
                Route::get('transactions', [TokenTransactionController::class, 'index'])->name('transactions.index');
            });
            // Content
            Route::resource('contents', ContentController::class)
             ->only(['index', 'create', 'store', 'edit', 'update']);
        
            // Token adjustment on user edit
            Route::post('/users/{user}/adjust-tokens', [UsersController::class, 'adjustTokens'])->name('users.adjust-tokens');
            
            // Unsupported Device Reports
            // Unsupported Device Reports
Route::prefix('unsupported-devices')->name('unsupported-devices.')->group(function () {
    Route::get('/', [UnsupportedDeviceReportController::class, 'index'])->name('index');
    Route::get('/export', [UnsupportedDeviceReportController::class, 'export'])->name('export');
    Route::post('/bulk-delete', [UnsupportedDeviceReportController::class, 'bulkDelete'])->name('bulk-delete');
    Route::get('/{id}', [UnsupportedDeviceReportController::class, 'show'])->name('show');
    Route::patch('/{id}/status', [UnsupportedDeviceReportController::class, 'updateStatus'])->name('update-status');
    Route::delete('/{id}', [UnsupportedDeviceReportController::class, 'destroy'])->name('destroy');

    });
        });
    });
});