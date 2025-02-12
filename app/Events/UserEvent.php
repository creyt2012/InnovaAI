<?php

namespace App\Events;

class UserEvent implements ShouldBroadcast
{
    public $type;
    public $data;
    public $timestamp;
    
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;
        $this->timestamp = now();
    }

    public function broadcastOn()
    {
        return new Channel('analytics');
    }
} 