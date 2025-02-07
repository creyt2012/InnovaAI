<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LMStudioService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.lmstudio.url');
    }

    public function sendMessage($message)
    {
        try {
            $response = Http::post($this->apiUrl, [
                'message' => $message,
                'temperature' => 0.7,
                'max_tokens' => 1000
            ]);

            return $response->json()['response'] ?? '';
        } catch (\Exception $e) {
            \Log::error('LMStudio API Error: ' . $e->getMessage());
            return 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại sau.';
        }
    }
} 