<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class AnalyticsException extends Exception
{
    public function report()
    {
        Log::channel('analytics')->error($this->getMessage(), [
            'exception' => $this,
            'trace' => $this->getTraceAsString()
        ]);
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->getMessage(),
                'code' => $this->getCode()
            ], 500);
        }

        return back()->with('error', $this->getMessage());
    }
} 