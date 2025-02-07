<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemAlert;
use Illuminate\Support\Facades\Cache;

class AlertService
{
    protected $thresholds = [
        'cpu_usage' => 80,
        'memory_usage' => 80,
        'disk_usage' => 85,
        'response_time' => 2000, // ms
        'error_rate' => 5, // %
        'concurrent_users' => 1000
    ];

    public function monitor()
    {
        $this->checkServerMetrics();
        $this->checkUserActivity();
        $this->checkSecurityIssues();
        $this->checkSystemHealth();
    }

    protected function checkServerMetrics()
    {
        $servers = Server::with('latestMetrics')->get();

        foreach ($servers as $server) {
            $metrics = $server->latestMetrics;
            if (!$metrics) continue;

            // CPU Alert
            if ($metrics->cpu_usage > $this->thresholds['cpu_usage']) {
                $this->createAlert('high_cpu', 'warning', [
                    'server_id' => $server->id,
                    'cpu_usage' => $metrics->cpu_usage
                ]);
            }

            // Memory Alert
            if ($metrics->memory_usage > $this->thresholds['memory_usage']) {
                $this->createAlert('high_memory', 'warning', [
                    'server_id' => $server->id,
                    'memory_usage' => $metrics->memory_usage
                ]);
            }

            // Response Time Alert
            if ($metrics->response_time > $this->thresholds['response_time']) {
                $this->createAlert('high_latency', 'error', [
                    'server_id' => $server->id,
                    'response_time' => $metrics->response_time
                ]);
            }
        }
    }

    protected function checkUserActivity()
    {
        $activeUsers = Cache::get('active_users', 0);
        
        if ($activeUsers > $this->thresholds['concurrent_users']) {
            $this->createAlert('high_user_load', 'warning', [
                'active_users' => $activeUsers,
                'threshold' => $this->thresholds['concurrent_users']
            ]);
        }

        // Check for suspicious activity
        $this->detectAnomalies();
    }

    protected function checkSecurityIssues()
    {
        // Check failed login attempts
        $failedLogins = Cache::get('failed_logins', []);
        foreach ($failedLogins as $ip => $attempts) {
            if ($attempts > 5) {
                $this->createAlert('brute_force_attempt', 'critical', [
                    'ip' => $ip,
                    'attempts' => $attempts
                ]);
            }
        }

        // Check API usage patterns
        $this->detectAPIAbuse();
    }

    protected function createAlert($type, $severity, $data)
    {
        // Prevent duplicate alerts
        $cacheKey = "alert:{$type}:" . md5(json_encode($data));
        if (Cache::has($cacheKey)) return;

        $alert = Alert::create([
            'type' => $type,
            'severity' => $severity,
            'data' => $data,
            'status' => 'active'
        ]);

        // Cache to prevent duplicates
        Cache::put($cacheKey, true, now()->addMinutes(30));

        // Notify relevant users
        $this->notifyAlert($alert);
    }

    protected function notifyAlert($alert)
    {
        $admins = User::role('admin')->get();
        
        Notification::send($admins, new SystemAlert($alert));

        // For critical alerts, send SMS/call
        if ($alert->severity === 'critical') {
            $this->sendUrgentNotification($alert);
        }
    }
} 