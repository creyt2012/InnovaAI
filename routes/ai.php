<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AI\ChatController;
use App\Http\Controllers\AI\ConversationController;

Route::middleware(['auth:api', 'throttle:ai_chat'])->group(function () {
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
    Route::put('/conversations/{conversation}', [ConversationController::class, 'update']);
    Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy']);
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
}); 