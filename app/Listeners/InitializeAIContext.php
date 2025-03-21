<?php

namespace App\Listeners;

use App\Events\ConversationCreated;

class InitializeAIContext
{
    public function handle(ConversationCreated $event)
    {
        // Initialize AI context for new conversation
    }
} 