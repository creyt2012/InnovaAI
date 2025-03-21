<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AIResponseGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $response;
    public $conversation;
    protected $user;

    public function __construct($response, $conversation, $user)
    {
        $this->response = $response;
        $this->conversation = $conversation;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('conversation.' . $this->conversation->id),
            new PrivateChannel('ai.processing.' . $this->user->id)
        ];
    }

    public function broadcastAs()
    {
        return 'ai.response';
    }

    public function broadcastWith()
    {
        return [
            'response' => $this->response,
            'conversation_id' => $this->conversation->id,
            'timestamp' => now()->toIso8601String()
        ];
    }
} 