<?php

use App\Http\Controllers\Admin\LmStudioApiController;
use App\Http\Controllers\Admin\ServerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminModelController;
use App\Http\Controllers\Admin\AdminServerController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminBankAccountController;
use App\Http\Controllers\Admin\AdminTransactionController;
use App\Http\Controllers\Admin\AdminAnalyticsController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');

    Route::resource('apis', LmStudioApiController::class);
    Route::resource('servers', ServerController::class);
    
    // Logs và Export routes
    Route::get('/logs', [AdminLogController::class, 'index'])->name('admin.logs');
    Route::get('/logs/export', [LogController::class, 'export'])->name('admin.logs.export');
    
    // Server monitoring
    Route::get('/monitoring', [MonitoringController::class, 'index'])
        ->name('monitoring');
    Route::get('/monitoring/stats', [MonitoringController::class, 'stats'])
        ->name('monitoring.stats');

    Route::post('apis/{api}/test', [LmStudioApiController::class, 'testConnection'])
        ->name('apis.test');

    Route::post('servers/{server}/test', [ServerController::class, 'test'])
        ->name('servers.test');

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('admin.settings');
    Route::get('/models', [AdminModelController::class, 'index'])->name('admin.models');
    Route::get('/servers', [AdminServerController::class, 'index'])->name('admin.servers');
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::get('/bank-accounts', [AdminBankAccountController::class, 'index'])->name('bank-accounts.index');
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/message', [ChatController::class, 'message'])->name('chat.message');
    Route::delete('/chat/{conversation}', [ChatController::class, 'destroy'])->name('chat.destroy');
});

Route::get('language/{locale}', [LanguageController::class, 'switch'])
    ->name('language.switch');

Route::get('language/detect', [LanguageController::class, 'detect'])
    ->name('language.detect');

// API Documentation
Route::middleware(['documentation.auth'])->group(function () {
    Route::get('/docs', function () {
        return redirect('/docs/index.html');
    })->name('api.docs');
}); 