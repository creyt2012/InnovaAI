<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Cache;

class ChatMemoryService
{
    protected $maxContextLength = 4096;
    protected $memoryTTL = 86400; // 24 hours

    public function storeContext($chatId, $context)
    {
        $key = "chat:{$chatId}:context";
        Cache::put($key, $context, $this->memoryTTL);
    }

    public function getContext($chatId)
    {
        return Cache::get("chat:{$chatId}:context");
    }

    public function summarizeChat($chatId)
    {
        $chat = Chat::with('messages')->find($chatId);
        $messages = $chat->messages->pluck('content')->join("\n");
        
        // Sử dụng AI để tóm tắt
        $summary = $this->generateSummary($messages);
        
        return $summary;
    }

    public function generateSummary($text)
    {
        // Tích hợp với LLM để tạo tóm tắt
        return "Summary of the conversation...";
    }

    public function pruneContext($context)
    {
        if (strlen($context) > $this->maxContextLength) {
            // Cắt bớt context cũ nhất
            return substr($context, -$this->maxContextLength);
        }
        return $context;
    }
} 