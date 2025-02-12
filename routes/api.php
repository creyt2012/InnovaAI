<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ServerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\AnalyticsController;

/**
 * @OA\Info(
 *     title="Analytics API",
 *     version="1.0.0",
 *     description="API documentation for analytics features"
 * )
 */

Route::middleware('auth:sanctum')->group(function () {
    // Chat routes
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/{id}', [ChatController::class, 'show']);
    Route::post('/chats/{id}/message', [ChatController::class, 'message']);
    Route::delete('/chats/{id}', [ChatController::class, 'destroy']);
    Route::post('/chat', [ChatController::class, 'chat']);
    Route::get('/chat/history', [ChatController::class, 'history']);

    // Server management routes (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/servers', [ServerController::class, 'index']);
        Route::post('/servers', [ServerController::class, 'store']);
        Route::get('/servers/{id}', [ServerController::class, 'show']);
        Route::put('/servers/{id}', [ServerController::class, 'update']);
        Route::delete('/servers/{id}', [ServerController::class, 'destroy']);
        Route::post('/servers/{id}/metrics', [ServerController::class, 'metrics']);
        Route::post('/servers/{id}/test', [ServerController::class, 'test']);
    });

    // Models
    Route::get('/models', [ModelController::class, 'index']);
    Route::get('/models/{model}/status', [ModelController::class, 'status']);
    
    // User preferences
    Route::post('/user/preferences/model', [UserPreferenceController::class, 'updateModel']);
    Route::post('/user/preferences/parameters', [UserPreferenceController::class, 'updateParameters']);

    // Settings routes (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', [SettingController::class, 'index']);
        Route::post('/settings', [SettingController::class, 'update']);
    });
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    // Package routes
    Route::apiResource('packages', PackageController::class);
    Route::post('packages/{package}/toggle', [PackageController::class, 'toggle']);
    Route::post('packages/reorder', [PackageController::class, 'reorder']);

    // Bank account routes
    Route::apiResource('bank-accounts', BankAccountController::class);
    Route::post('bank-accounts/{bankAccount}/toggle', [BankAccountController::class, 'toggle']);
    Route::post('bank-accounts/reorder', [BankAccountController::class, 'reorder']);

    // Transaction routes
    Route::get('transactions', [TransactionController::class, 'index']);
    Route::get('transactions/export', [TransactionController::class, 'export']);
    Route::post('transactions/{transaction}/update-status', [TransactionController::class, 'updateStatus']);
});

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Public package routes
Route::get('packages', [PackageController::class, 'getActivePackages']);

Route::middleware(['auth:sanctum', 'analytics.ratelimit'])->group(function () {
    /**
     * @OA\Get(
     *     path="/api/analytics/stats",
     *     summary="Get analytics statistics",
     *     tags={"Analytics"},
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="429", description="Too Many Requests")
     * )
     */
    Route::get('/analytics/stats', [AnalyticsController::class, 'stats']);
}); 