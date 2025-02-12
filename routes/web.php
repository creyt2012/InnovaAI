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
use App\Http\Controllers\Admin\LMStudioNodeController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\Admin\LandingPageController;

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

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/logo', [SettingController::class, 'updateLogo'])->name('settings.logo');
    Route::post('/settings/ai-model', [SettingController::class, 'updateAIModel'])->name('settings.ai-model');

    Route::get('/models', [AdminModelController::class, 'index'])->name('admin.models');
    Route::get('/servers', [AdminServerController::class, 'index'])->name('admin.servers');
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::get('/bank-accounts', [AdminBankAccountController::class, 'index'])->name('bank-accounts.index');
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');

    Route::resource('lmstudio/nodes', LMStudioNodeController::class);
    Route::post('lmstudio/nodes/{node}/test', [LMStudioNodeController::class, 'test'])
        ->name('lmstudio.nodes.test');

    Route::get('/landing-page', [LandingPageController::class, 'edit'])
        ->name('landing-page.edit');
    Route::put('/landing-page', [LandingPageController::class, 'update'])
        ->name('landing-page.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/message', [ChatController::class, 'message'])->name('chat.message');
    Route::delete('/chat/{conversation}', [ChatController::class, 'destroy'])->name('chat.destroy');

    // User preferences
    Route::get('/preferences', [UserPreferenceController::class, 'show']);
    Route::post('/preferences', [UserPreferenceController::class, 'update']);
    
    // Chat exports
    Route::post('/chat/{chat}/export', [ChatController::class, 'export']);
    Route::get('/exports/{filename}', [ChatController::class, 'downloadExport']);
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