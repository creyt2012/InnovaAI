<?php

namespace App\Exceptions;

use Exception;

class InvalidAIResponseException extends Exception
{
    public function __construct($message = "Invalid response from AI service", $code = 422)
    {
        parent::__construct($message, $code);
    }
} 