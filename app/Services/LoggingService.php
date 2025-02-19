<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class LoggingService
{
    protected $logger;

    public function __construct()
    {
        $this->logger = new Logger('chat');
        $this->logger->pushHandler(
            new RotatingFileHandler(
                storage_path('logs/chat.log'),
                30,
                Logger::INFO
            )
        );
    }

    public function logChat($userId, $message, $response)
    {
        $this->logger->info('Chat interaction', [
            'user_id' => $userId,
            'message' => $message,
            'response' => $response,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
} 