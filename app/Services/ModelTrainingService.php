<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ModelTrainingService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.training.url');
        $this->apiKey = config('services.training.key');
    }

    public function prepareTrainingData($data)
    {
        // Chuẩn bị dữ liệu training
        $formattedData = $this->formatData($data);
        $validatedData = $this->validateData($formattedData);
        
        return $validatedData;
    }

    public function startTraining($data, $parameters = [])
    {
        $trainingData = $this->prepareTrainingData($data);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->post($this->baseUrl . '/train', [
            'data' => $trainingData,
            'parameters' => $parameters
        ]);

        return $response->json();
    }

    public function monitorTraining($jobId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->get($this->baseUrl . '/jobs/' . $jobId);

        return $response->json();
    }

    public function evaluateModel($modelId, $testData)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey
        ])->post($this->baseUrl . '/evaluate', [
            'model_id' => $modelId,
            'test_data' => $testData
        ]);

        return $response->json();
    }
} 