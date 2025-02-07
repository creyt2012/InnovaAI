<?php

namespace App\Services;

use App\Models\Server;
use App\Models\ScalingPrediction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PredictiveScalingService
{
    protected $historicalData;
    protected $predictions;

    public function analyze()
    {
        $this->collectHistoricalData();
        $this->generatePredictions();
        $this->adjustCapacity();
    }

    protected function collectHistoricalData()
    {
        // Thu thập dữ liệu theo giờ trong 7 ngày qua
        $this->historicalData = Server::with(['metrics' => function ($query) {
            $query->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at');
        }])->get()->map(function ($server) {
            return [
                'server_id' => $server->id,
                'hourly_metrics' => $this->aggregateHourlyMetrics($server->metrics)
            ];
        });
    }

    protected function aggregateHourlyMetrics($metrics)
    {
        return $metrics->groupBy(function ($metric) {
            return $metric->created_at->format('Y-m-d H:00:00');
        })->map(function ($hourMetrics) {
            return [
                'cpu_avg' => $hourMetrics->avg('cpu_usage'),
                'memory_avg' => $hourMetrics->avg('memory_usage'),
                'connections_avg' => $hourMetrics->avg('active_connections'),
                'requests_avg' => $hourMetrics->avg('requests_per_minute')
            ];
        });
    }

    protected function generatePredictions()
    {
        $this->predictions = collect();

        // Phân tích pattern theo ngày trong tuần và giờ
        foreach ($this->historicalData as $serverData) {
            $patterns = $this->analyzeDailyPatterns($serverData['hourly_metrics']);
            
            // Dự đoán cho 24h tiếp theo
            for ($hour = 0; $hour < 24; $hour++) {
                $prediction = $this->predictLoad($patterns, $hour);
                
                $this->predictions->push([
                    'server_id' => $serverData['server_id'],
                    'hour' => $hour,
                    'predicted_cpu' => $prediction['cpu'],
                    'predicted_memory' => $prediction['memory'],
                    'predicted_connections' => $prediction['connections'],
                    'confidence' => $prediction['confidence']
                ]);
            }
        }

        // Cache predictions
        Cache::put('scaling_predictions', $this->predictions, now()->addHours(1));
    }

    protected function analyzeDailyPatterns($metrics)
    {
        $patterns = [];
        
        foreach ($metrics as $datetime => $metric) {
            $dayOfWeek = Carbon::parse($datetime)->dayOfWeek;
            $hour = Carbon::parse($datetime)->hour;
            
            if (!isset($patterns[$dayOfWeek][$hour])) {
                $patterns[$dayOfWeek][$hour] = [];
            }
            
            $patterns[$dayOfWeek][$hour][] = $metric;
        }

        return $patterns;
    }

    protected function predictLoad($patterns, $hour)
    {
        $dayOfWeek = now()->dayOfWeek;
        $historicalData = $patterns[$dayOfWeek][$hour] ?? [];
        
        if (empty($historicalData)) {
            return [
                'cpu' => 50, // Default predictions
                'memory' => 50,
                'connections' => 10,
                'confidence' => 0.5
            ];
        }

        // Calculate averages and standard deviations
        $cpuValues = array_column($historicalData, 'cpu_avg');
        $memoryValues = array_column($historicalData, 'memory_avg');
        $connectionValues = array_column($historicalData, 'connections_avg');

        return [
            'cpu' => $this->calculatePrediction($cpuValues),
            'memory' => $this->calculatePrediction($memoryValues),
            'connections' => $this->calculatePrediction($connectionValues),
            'confidence' => $this->calculateConfidence($cpuValues)
        ];
    }

    protected function calculatePrediction($values)
    {
        $avg = array_sum($values) / count($values);
        $stdDev = $this->standardDeviation($values);
        
        // Add safety margin based on standard deviation
        return $avg + ($stdDev * 1.5);
    }

    protected function calculateConfidence($values)
    {
        $stdDev = $this->standardDeviation($values);
        $mean = array_sum($values) / count($values);
        
        // Calculate coefficient of variation
        $cv = ($stdDev / $mean);
        
        // Convert to confidence score (0-1)
        return max(0, min(1, 1 - $cv));
    }

    protected function standardDeviation($values)
    {
        $avg = array_sum($values) / count($values);
        $squaredDiffs = array_map(function ($value) use ($avg) {
            return pow($value - $avg, 2);
        }, $values);
        
        return sqrt(array_sum($squaredDiffs) / count($values));
    }

    protected function adjustCapacity()
    {
        $predictions = Cache::get('scaling_predictions');
        if (!$predictions) return;

        $nextHourPredictions = $predictions->where('hour', now()->addHour()->hour);
        
        foreach ($nextHourPredictions as $prediction) {
            if ($prediction['confidence'] < 0.7) continue;

            $server = Server::find($prediction['server_id']);
            if (!$server) continue;

            // Pre-scale if high load predicted
            if ($prediction['predicted_cpu'] > 70 || $prediction['predicted_memory'] > 70) {
                Log::info('Predictive scaling up', [
                    'server_id' => $server->id,
                    'predictions' => $prediction
                ]);
                
                // Trigger auto-scaling
                app(AutoScalingService::class)->scaleUp();
            }
        }
    }
} 