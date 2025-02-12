<?php

namespace App\Services;

use Google\Cloud\Language\V1\LanguageServiceClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ContentModerationService
{
    protected $client;
    protected $toxicityThreshold = 0.7;

    public function __construct()
    {
        $this->client = new LanguageServiceClient();
    }

    public function moderateContent($text)
    {
        $cacheKey = 'moderation:' . md5($text);
        
        return Cache::remember($cacheKey, 3600, function() use ($text) {
            $document = $this->client->analyzeEntitySentiment($text);
            
            return [
                'toxicity' => $this->analyzeToxicity($text),
                'sentiment' => $this->analyzeSentiment($text),
                'entities' => $this->extractEntities($document),
                'is_safe' => $this->isSafeContent($text)
            ];
        });
    }

    protected function analyzeToxicity($text)
    {
        // Perspective API integration
        $response = Http::post(config('services.perspective.url'), [
            'comment' => ['text' => $text],
            'languages' => ['vi'],
            'requestedAttributes' => [
                'TOXICITY' => [],
                'SEVERE_TOXICITY' => [],
                'IDENTITY_ATTACK' => [],
                'THREAT' => []
            ]
        ]);

        return $response->json()['attributeScores'];
    }

    protected function isSafeContent($text)
    {
        $toxicity = $this->analyzeToxicity($text);
        return $toxicity['TOXICITY']['summaryScore']['value'] < $this->toxicityThreshold;
    }

    public function filterSensitiveData($text)
    {
        // Lọc thông tin nhạy cảm như email, số điện thoại, CMND...
        $patterns = [
            'email' => '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}/i',
            'phone' => '/(84|0[3|5|7|8|9])+([0-9]{8})\b/',
            'id_card' => '/\d{9,12}/'
        ];

        foreach ($patterns as $type => $pattern) {
            $text = preg_replace($pattern, '[FILTERED_' . strtoupper($type) . ']', $text);
        }

        return $text;
    }
} 