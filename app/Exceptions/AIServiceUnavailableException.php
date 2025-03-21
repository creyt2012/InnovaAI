<?php

namespace App\Exceptions;

use Exception;

class AIServiceUnavailableException extends Exception
{
    public function __construct($message = "AI Service is temporarily unavailable", $code = 503)
    {
        parent::__construct($message, $code);
    }
} 