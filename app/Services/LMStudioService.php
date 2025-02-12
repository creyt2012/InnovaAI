<?php

namespace App\Services;

use App\Models\LMStudioNode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LMStudioService
{
    protected $nodes = [];
    protected $currentNode = 0;

    public function __construct()
    {
        $this->nodes = Cache::tags('lmstudio_nodes')->remember('active_nodes', 60, function () {
            return LMStudioNode::where('is_active', true)
                ->orderBy('priority')
                ->get();
        });
    }

    public function sendMessage($message)
    {
        $attempts = count($this->nodes);
        $exception = null;

        while ($attempts > 0) {
            try {
                $node = $this->getNextNode();
                
                if (!$this->isNodeHealthy($node)) {
                    continue;
                }

                $response = Http::timeout($node->timeout)->post($node->url, [
                    'message' => $message,
                    'temperature' => $node->temperature,
                    'max_tokens' => $node->max_tokens
                ]);

                if ($response->successful()) {
                    $this->updateNodeMetrics($node, true);
                    return $response->json()['response'] ?? '';
                }

            } catch (\Exception $e) {
                $exception = $e;
                $this->updateNodeMetrics($node, false);
                Log::error("LMStudio API Error at node {$node->name}: " . $e->getMessage());
            }

            $attempts--;
        }

        Log::error('All LMStudio nodes failed');
        throw $exception ?? new \Exception('Không thể kết nối tới LMStudio');
    }

    protected function getNextNode()
    {
        // Round-robin load balancing
        $this->currentNode = ($this->currentNode + 1) % count($this->nodes);
        return $this->nodes[$this->currentNode];
    }

    protected function isNodeHealthy($node)
    {
        $cacheKey = "lmstudio_health_{$node->url}";
        
        // Kiểm tra cache trước
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout(5)->get($node->url . '/health');
            $isHealthy = $response->successful();
            
            // Cache kết quả trong 1 phút
            Cache::put($cacheKey, $isHealthy, now()->addMinute());
            
            return $isHealthy;
        } catch (\Exception $e) {
            Cache::put($cacheKey, false, now()->addMinute());
            return false;
        }
    }

    protected function updateNodeMetrics($node, $success)
    {
        $metrics = Cache::get('lmstudio_metrics', []);
        
        if (!isset($metrics[$node->url])) {
            $metrics[$node->url] = [
                'success' => 0,
                'failure' => 0,
                'last_success' => null,
                'last_failure' => null
            ];
        }

        if ($success) {
            $metrics[$node->url]['success']++;
            $metrics[$node->url]['last_success'] = now();
        } else {
            $metrics[$node->url]['failure']++;
            $metrics[$node->url]['last_failure'] = now();
        }

        Cache::put('lmstudio_metrics', $metrics, now()->addDay());
    }
} 