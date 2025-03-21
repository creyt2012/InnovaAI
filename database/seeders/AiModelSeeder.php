<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiModel;

class AiModelSeeder extends Seeder
{
    public function run()
    {
        AiModel::create([
            'name' => 'LMStudio Node 1',
            'endpoint' => 'http://localhost:1234/v1/chat/completions',
            'type' => 'lmstudio',
            'status' => 'active',
            'priority' => 1,
        ]);

        AiModel::create([
            'name' => 'LMStudio Node 2',
            'endpoint' => 'http://localhost:1235/v1/chat/completions',
            'type' => 'lmstudio',
            'status' => 'active',
            'priority' => 2,
        ]);

        // Fallback OpenAI
        AiModel::create([
            'name' => 'OpenAI GPT-3.5',
            'endpoint' => 'https://api.openai.com/v1/chat/completions',
            'type' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'status' => 'active',
            'priority' => 0,
        ]);
    }
} 