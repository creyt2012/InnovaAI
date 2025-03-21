<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Models\Message;
use App\Models\Conversation;
use App\Events\ChatMessageSent;
use App\Events\AIResponseGenerated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $aiService;

    public function __construct()
    {
        $this->aiService = new AIService(Auth::user()->preferredModel);
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Lưu tin nhắn của user
        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'content' => $request->message,
            'role' => 'user'
        ]);

        // Broadcast tin nhắn user
        broadcast(new ChatMessageSent($userMessage, Auth::user(), $conversation))->toOthers();

        // Lấy context từ conversation
        $context = [
            'system' => $conversation->system_prompt,
            'history' => $conversation->messages()
                ->latest()
                ->take(config('ai.max_conversation_history'))
                ->get()
                ->map(function ($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->reverse()
                ->values()
                ->toArray()
        ];

        // Gọi AI để generate response
        $aiResponse = $this->aiService->generateResponse($request->message, $context);

        // Lưu response của AI
        $aiMessage = Message::create([
            'conversation_id' => $conversation->id,
            'content' => $aiResponse,
            'role' => 'assistant'
        ]);

        // Broadcast AI response
        broadcast(new AIResponseGenerated($aiMessage, $conversation, Auth::user()));

        return response()->json([
            'message' => $aiMessage,
            'conversation' => $conversation->fresh()
        ]);
    }
} 