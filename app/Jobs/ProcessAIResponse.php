<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AIService;
use App\Models\Message;
use App\Events\AIResponseGenerated;

class ProcessAIResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $conversation;

    public function __construct($message, $conversation)
    {
        $this->message = $message;
        $this->conversation = $conversation;
    }

    public function handle(AIService $aiService)
    {
        $response = $aiService->generateResponse(
            $this->message->content,
            ['conversation_id' => $this->conversation->id]
        );

        $aiMessage = Message::create([
            'conversation_id' => $this->conversation->id,
            'content' => $response,
            'role' => 'assistant'
        ]);

        event(new AIResponseGenerated($aiMessage, $this->conversation));
    }
} 