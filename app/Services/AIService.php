<?php

namespace App\Services;

use App\Exceptions\AIServiceUnavailableException;
use App\Exceptions\InvalidAIResponseException;
use App\Models\AiModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIService
{
    protected $model;
    
    public function __construct(?AiModel $preferredModel = null)
    {
        $this->model = $preferredModel ?? $this->getAvailableModel();
    }

    protected function getAvailableModel()
    {
        // Cache available model for 1 minute to reduce DB queries
        return Cache::remember('available_ai_model', 60, function () {
            return AiModel::where('status', 'active')
                         ->orderBy('priority', 'desc')
                         ->first();
        });
    }

    public function generateResponse($message, $context = [])
    {
        try {
            $response = $this->makeRequest($message, $context);

            if (!$response->successful()) {
                // If current model fails, try other models
                $fallbackModel = $this->getFallbackModel();
                if ($fallbackModel && $fallbackModel->id !== $this->model->id) {
                    $this->model = $fallbackModel;
                    $response = $this->makeRequest($message, $context);
                }

                if (!$response->successful()) {
                    Log::error('AI Service Error', [
                        'model' => $this->model->name,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    throw new AIServiceUnavailableException();
                }
            }

            return $this->parseResponse($response);

        } catch (\Exception $e) {
            Log::error('AI Service Exception', [
                'model' => $this->model->name,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new AIServiceUnavailableException();
        }
    }

    protected function makeRequest($message, $context)
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($this->model->api_key) {
            $headers['Authorization'] = 'Bearer ' . $this->model->api_key;
        }

        return Http::withHeaders($headers)
            ->timeout(30)
            ->post($this->model->endpoint, [
                'messages' => $this->formatMessages($message, $context),
                'max_tokens' => $this->model->max_tokens,
                'temperature' => $this->model->temperature,
                'presence_penalty' => 0.6,
                'frequency_penalty' => 0.0,
            ]);
    }

    protected function getFallbackModel()
    {
        return AiModel::where('status', 'active')
                     ->where('id', '!=', $this->model->id)
                     ->orderBy('priority', 'desc')
                     ->first();
    }

    protected function parseResponse($response)
    {
        $data = $response->json();
        
        if ($this->model->type === 'openai') {
            return $data['choices'][0]['message']['content'];
        }

        // LMStudio format
        if ($this->model->type === 'lmstudio') {
            return $data['response'] ?? $data['choices'][0]['message']['content'];
        }

        throw new InvalidAIResponseException();
    }

    protected function formatMessages($message, $context)
    {
        $messages = [];

        // Add system context if available
        if (!empty($context['system'])) {
            $messages[] = [
                'role' => 'system',
                'content' => $context['system']
            ];
        }

        // Add conversation history
        if (!empty($context['history'])) {
            foreach ($context['history'] as $msg) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }
        }

        // Add current message
        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];

        return $messages;
    }
} 