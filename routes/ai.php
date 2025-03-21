<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AI\ChatController;
use App\Http\Controllers\AI\ConversationController;

Route::get('/conversations', [ConversationController::class, 'index']);
Route::post('/conversations', [ConversationController::class, 'store']);
Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']); 