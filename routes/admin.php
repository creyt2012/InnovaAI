<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SystemConfigController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\LogViewerController;
use App\Http\Controllers\Admin\ApiManagementController;
use App\Http\Controllers\Admin\NotificationController;

Route::middleware(['auth', 'admin'])->group(function () {
    // System Config
    Route::get('config', [SystemConfigController::class, 'index'])->name('config.index');
    Route::post('config', [SystemConfigController::class, 'update'])->name('config.update');
    Route::post('config/maintenance', [SystemConfigController::class, 'maintenance'])->name('config.maintenance');

    // Security
    Route::get('security', [SecurityController::class, 'index'])->name('security.index');
    Route::post('security/block-ip', [SecurityController::class, 'blockIp'])->name('security.block-ip');
    Route::get('security/audit', [SecurityController::class, 'auditLog'])->name('security.audit');

    // Logs
    Route::get('logs', [LogViewerController::class, 'index'])->name('logs.index');
    Route::get('logs/{filename}', [LogViewerController::class, 'show'])->name('logs.show');
    Route::get('logs/{filename}/download', [LogViewerController::class, 'download'])->name('logs.download');

    // API Management
    Route::resource('api', ApiManagementController::class);
    Route::post('api/{endpoint}/rate-limit', [ApiManagementController::class, 'rateLimit'])->name('api.rate-limit');
    Route::get('api/monitor', [ApiManagementController::class, 'monitor'])->name('api.monitor');

    // Notifications
    Route::resource('notifications', NotificationController::class);
    Route::post('notifications/broadcast', [NotificationController::class, 'broadcast'])->name('notifications.broadcast');
}); 