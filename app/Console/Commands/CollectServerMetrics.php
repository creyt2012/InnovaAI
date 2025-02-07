<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CollectServerMetrics extends Command
{
    protected $signature = 'metrics:collect';
    protected $description = 'Collect server performance metrics';

    public function handle()
    {
        $metrics = [
            'memory_usage' => memory_get_usage(true),
            'cpu_load' => sys_getloadavg(),
            'redis_used_memory' => $this->getRedisMemory(),
            'php_workers' => $this->getPhpWorkers(),
            'request_rate' => $this->getRequestRate(),
        ];

        Redis::hMSet('server:metrics', $metrics);
        Redis::expire('server:metrics', 300); // Expire after 5 minutes

        $this->info('Metrics collected successfully');
    }

    private function getRedisMemory()
    {
        $info = Redis::info();
        return $info['used_memory'] ?? 0;
    }

    private function getPhpWorkers()
    {
        exec("ps aux | grep php-fpm | wc -l", $output);
        return (int)($output[0] ?? 0);
    }

    private function getRequestRate()
    {
        return Redis::incr('metrics:requests:'.date('Y-m-d-H'));
    }
} 