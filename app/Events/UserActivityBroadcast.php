<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActivityBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $activity;
    public $screenshot;
    public $timestamp;

    public function __construct($userId, $activity, $screenshot = null)
    {
        $this->userId = $userId;
        $this->activity = $activity;
        $this->screenshot = $screenshot;
        $this->timestamp = now();
    }

    public function broadcastOn()
    {
        return new Channel('user-monitoring.' . $this->userId);
    }
} 