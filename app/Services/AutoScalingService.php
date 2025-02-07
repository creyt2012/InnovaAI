<?php

namespace App\Services;

use App\Models\Server;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AutoScalingService
{
    protected $config;
    protected $metrics;

    public function __construct()
    {
        $this->config = [
            'min_instances' => config('scaling.min_instances', 1),
            'max_instances' => config('scaling.max_instances', 5),
            'scale_up_threshold' => config('scaling.scale_up_threshold', 80), // CPU/Memory > 80%
            'scale_down_threshold' => config('scaling.scale_down_threshold', 30), // CPU/Memory < 30%
            'cooldown_period' => config('scaling.cooldown_period', 300), // 5 minutes
        ];
    }

    public function evaluate()
    {
        if ($this->isInCooldown()) {
            return;
        }

        $this->collectMetrics();

        if ($this->shouldScaleUp()) {
            $this->scaleUp();
        } elseif ($this->shouldScaleDown()) {
            $this->scaleDown();
        }
    }

    protected function collectMetrics()
    {
        $activeServers = Server::where('status', 'active')
            ->with('latestMetrics')
            ->get();

        $this->metrics = [
            'total_servers' => $activeServers->count(),
            'avg_cpu' => $activeServers->avg('latestMetrics.cpu_usage'),
            'avg_memory' => $activeServers->avg('latestMetrics.memory_usage'),
            'total_connections' => $activeServers->sum('latestMetrics.active_connections'),
            'avg_response_time' => $activeServers->avg('latestMetrics.response_time'),
        ];

        Log::info('Current scaling metrics', $this->metrics);
    }

    protected function shouldScaleUp(): bool
    {
        if ($this->metrics['total_servers'] >= $this->config['max_instances']) {
            return false;
        }

        return $this->metrics['avg_cpu'] > $this->config['scale_up_threshold'] ||
               $this->metrics['avg_memory'] > $this->config['scale_up_threshold'] ||
               $this->metrics['avg_response_time'] > 2000; // >2s response time
    }

    protected function shouldScaleDown(): bool
    {
        if ($this->metrics['total_servers'] <= $this->config['min_instances']) {
            return false;
        }

        return $this->metrics['avg_cpu'] < $this->config['scale_down_threshold'] &&
               $this->metrics['avg_memory'] < $this->config['scale_down_threshold'] &&
               $this->metrics['avg_response_time'] < 1000; // <1s response time
    }

    protected function scaleUp()
    {
        try {
            // Lấy template server từ configuration
            $template = config('scaling.server_template');
            
            // Tạo server mới
            $server = Server::create([
                'name' => 'auto-scaled-' . time(),
                'host' => $template['host'],
                'port' => $this->getAvailablePort(),
                'status' => 'provisioning',
                'configuration' => $template['configuration'],
            ]);

            // Gọi API để provision server mới
            $response = Http::post(config('scaling.provisioner_url'), [
                'server_id' => $server->id,
                'template' => $template,
            ]);

            if ($response->successful()) {
                $server->update(['status' => 'active']);
                Log::info('Successfully scaled up new server', ['server_id' => $server->id]);
            } else {
                throw new \Exception('Failed to provision server: ' . $response->body());
            }

            $this->setCooldown();

        } catch (\Exception $e) {
            Log::error('Scale up failed: ' . $e->getMessage());
        }
    }

    protected function scaleDown()
    {
        try {
            // Tìm server ít tải nhất để shutdown
            $serverToRemove = Server::where('status', 'active')
                ->whereHas('latestMetrics', function ($query) {
                    $query->where('cpu_usage', '<', 30)
                          ->where('memory_usage', '<', 30)
                          ->where('active_connections', '<', 10);
                })
                ->orderBy('latestMetrics.active_connections')
                ->first();

            if (!$serverToRemove) {
                return;
            }

            // Đánh dấu server để không nhận request mới
            $serverToRemove->update(['status' => 'draining']);

            // Chờ các request hiện tại hoàn thành
            $this->drainConnections($serverToRemove);

            // Gọi API để decommission server
            $response = Http::delete(config('scaling.provisioner_url'), [
                'server_id' => $serverToRemove->id
            ]);

            if ($response->successful()) {
                $serverToRemove->delete();
                Log::info('Successfully scaled down server', ['server_id' => $serverToRemove->id]);
            } else {
                throw new \Exception('Failed to decommission server: ' . $response->body());
            }

            $this->setCooldown();

        } catch (\Exception $e) {
            Log::error('Scale down failed: ' . $e->getMessage());
        }
    }

    protected function isInCooldown(): bool
    {
        return Cache::has('autoscaling_cooldown');
    }

    protected function setCooldown()
    {
        Cache::put('autoscaling_cooldown', true, now()->addSeconds($this->config['cooldown_period']));
    }

    protected function getAvailablePort(): int
    {
        $usedPorts = Server::pluck('port')->toArray();
        $port = 8000;
        while (in_array($port, $usedPorts)) {
            $port++;
        }
        return $port;
    }

    protected function drainConnections(Server $server)
    {
        $maxWaitTime = 300; // 5 minutes
        $startTime = time();

        while (time() - $startTime < $maxWaitTime) {
            $activeConnections = $server->latestMetrics->active_connections ?? 0;
            if ($activeConnections == 0) {
                return true;
            }
            sleep(5);
        }

        Log::warning('Server drain timeout', ['server_id' => $server->id]);
        return false;
    }
} 