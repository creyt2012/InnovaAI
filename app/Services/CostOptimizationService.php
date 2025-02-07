<?php

namespace App\Services;

use App\Models\Server;
use App\Models\ServerMetric;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CostOptimizationService
{
    protected $metrics = [];
    protected $recommendations = [];

    public function analyze()
    {
        $this->collectMetrics();
        $this->analyzeUtilization();
        $this->analyzeIdleTime();
        $this->analyzeCosts();
        $this->generateRecommendations();
        
        return $this->recommendations;
    }

    protected function collectMetrics()
    {
        // Collect last 30 days of metrics
        $this->metrics = ServerMetric::with('server')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->groupBy('server_id');
    }

    protected function analyzeUtilization()
    {
        foreach ($this->metrics as $serverId => $serverMetrics) {
            $avgCpu = $serverMetrics->avg('cpu_usage');
            $avgMemory = $serverMetrics->avg('memory_usage');
            $peakCpu = $serverMetrics->max('cpu_usage');
            $peakMemory = $serverMetrics->max('memory_usage');

            // Check for underutilization
            if ($avgCpu < 30 && $peakCpu < 60) {
                $this->recommendations[] = [
                    'type' => 'underutilization',
                    'server_id' => $serverId,
                    'message' => "Server is consistently underutilized (Avg CPU: {$avgCpu}%)",
                    'action' => 'Consider downsizing or consolidating',
                    'potential_savings' => 'High'
                ];
            }

            // Check for over-provisioning
            if ($avgMemory < 40 && $peakMemory < 70) {
                $this->recommendations[] = [
                    'type' => 'over_provisioning',
                    'server_id' => $serverId,
                    'message' => "Memory is over-provisioned (Avg Usage: {$avgMemory}%)",
                    'action' => 'Reduce allocated memory',
                    'potential_savings' => 'Medium'
                ];
            }
        }
    }

    protected function analyzeIdleTime()
    {
        foreach ($this->metrics as $serverId => $serverMetrics) {
            $idleHours = $serverMetrics
                ->where('cpu_usage', '<', 10)
                ->where('active_connections', '<', 5)
                ->count();

            if ($idleHours > 48) { // More than 2 days worth of idle hours
                $this->recommendations[] = [
                    'type' => 'idle_time',
                    'server_id' => $serverId,
                    'message' => "Server has significant idle time ({$idleHours} hours)",
                    'action' => 'Consider implementing auto-shutdown during idle periods',
                    'potential_savings' => 'Medium'
                ];
            }
        }
    }

    protected function analyzeCosts()
    {
        $servers = Server::all();
        
        foreach ($servers as $server) {
            // Analyze cost per request
            $metrics = $this->metrics[$server->id] ?? collect();
            if ($metrics->isEmpty()) continue;

            $totalRequests = $metrics->sum('requests_per_minute') * 60;
            $serverCost = $this->calculateServerCost($server);
            
            if ($totalRequests > 0) {
                $costPerRequest = $serverCost / $totalRequests;
                
                if ($costPerRequest > 0.001) { // More than $0.001 per request
                    $this->recommendations[] = [
                        'type' => 'high_cost_per_request',
                        'server_id' => $server->id,
                        'message' => "High cost per request: \${$costPerRequest}",
                        'action' => 'Optimize request handling or consider different server type',
                        'potential_savings' => 'High'
                    ];
                }
            }
        }
    }

    protected function calculateServerCost($server)
    {
        // Implement your cost calculation logic here
        // This is just a placeholder
        return 100; // Monthly cost in USD
    }

    protected function generateRecommendations()
    {
        // Sort recommendations by potential savings
        $this->recommendations = collect($this->recommendations)
            ->sortByDesc('potential_savings')
            ->values()
            ->all();

        // Log recommendations
        Log::info('Cost optimization recommendations generated', [
            'count' => count($this->recommendations),
            'potential_savings' => collect($this->recommendations)
                ->where('potential_savings', 'High')
                ->count()
        ]);
    }
} 