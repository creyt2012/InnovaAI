<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AIService;

class TestAIConnection extends Command
{
    protected $signature = 'ai:test-connection';
    protected $description = 'Test connection to AI service';

    public function handle(AIService $aiService)
    {
        try {
            $response = $aiService->generateResponse('Test connection');
            $this->info('Connection successful!');
        } catch (\Exception $e) {
            $this->error('Connection failed: ' . $e->getMessage());
        }
    }
} 