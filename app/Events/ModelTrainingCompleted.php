<?php

namespace App\Events;

use App\Models\KnowledgeBase;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModelTrainingCompleted
{
    use Dispatchable, SerializesModels;

    public $knowledgeBase;

    public function __construct(KnowledgeBase $knowledgeBase)
    {
        $this->knowledgeBase = $knowledgeBase;
    }
} 