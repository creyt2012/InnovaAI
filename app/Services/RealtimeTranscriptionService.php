<?php

namespace App\Services;

use WebSocket\Client;
use Google\Cloud\Speech\V1\StreamingRecognizeRequest;
use Google\Cloud\Speech\V1\RecognitionConfig;

class RealtimeTranscriptionService
{
    protected $wsClient;
    protected $config;

    public function __construct()
    {
        $this->config = new RecognitionConfig([
            'encoding' => RecognitionConfig\AudioEncoding::WEBM_OPUS,
            'sample_rate_hertz' => 48000,
            'language_code' => 'vi-VN',
            'enable_automatic_punctuation' => true,
            'model' => 'latest_long',
            'use_enhanced' => true
        ]);
    }

    public function startStream()
    {
        $this->wsClient = new Client(config('services.google.speech_ws_url'));
        
        // Gửi config
        $request = new StreamingRecognizeRequest();
        $request->setStreamingConfig($this->config);
        $this->wsClient->send($request->serializeToString());

        return $this->wsClient;
    }

    public function processAudioChunk($audioData)
    {
        $request = new StreamingRecognizeRequest();
        $request->setAudioContent($audioData);
        $this->wsClient->send($request->serializeToString());

        $response = $this->wsClient->receive();
        return $this->parseResponse($response);
    }

    protected function parseResponse($response)
    {
        // Parse và trả về text theo thời gian thực
        return [
            'text' => $response->getResults()[0]->getAlternatives()[0]->getTranscript(),
            'is_final' => $response->getResults()[0]->getIsFinal()
        ];
    }
} 