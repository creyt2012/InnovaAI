<?php

return [
    // OpenAI Configuration
    'api_key' => env('OPENAI_API_KEY'),
    'api_url' => env('OPENAI_API_URL', 'https://api.openai.com'),
    'model' => env('AI_MODEL', 'gpt-3.5-turbo'),
    'max_tokens' => env('AI_MAX_TOKENS', 2000),
    'temperature' => env('AI_TEMPERATURE', 0.7),

    // Rate Limiting
    'rate_limits' => [
        'requests_per_minute' => env('AI_RATE_LIMIT_PER_MINUTE', 60),
        'tokens_per_minute' => env('AI_TOKEN_LIMIT_PER_MINUTE', 40000),
    ],

    // Context Settings
    'max_context_length' => env('AI_MAX_CONTEXT_LENGTH', 4096),
    'max_conversation_history' => env('AI_MAX_CONVERSATION_HISTORY', 10),

    // Response Settings
    'timeout' => env('AI_TIMEOUT', 30),
    'stream' => env('AI_STREAM', false),

    // Moderation
    'enable_moderation' => env('AI_ENABLE_MODERATION', true),
    'moderation_model' => env('AI_MODERATION_MODEL', 'text-moderation-latest'),
]; 