<?php

namespace App\Services;

use App\Models\Server;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BackupServerService
{
    protected $primaryServers;
    protected $backupServers;
    protected $healthChecks = [];

    public function __construct()
    {
        $this->primaryServers = Server::where('type', 'primary')->get();
        $this->backupServers = Server::where('type', 'backup')
            ->where('status', 'standby')
            ->get();
    }

    public function monitor()
    {
        $this->checkPrimaryServers();
        $this->updateBackupServers();
        $this->handleFailovers();
    }

    protected function checkPrimaryServers()
    {
        foreach ($this->primaryServers as $server) {
            try {
                $response = Http::timeout(5)
                    ->get("{$server->host}:{$server->port}/health");

                $this->healthChecks[$server->id] = [
                    'healthy' => $response->successful(),
                    'last_check' => now(),
                    'response_time' => $response->handlerStats()['total_time'] ?? null,
                ];

                if (!$response->successful()) {
                    Log::warning("Primary server health check failed", [
                        'server_id' => $server->id,
                        'response' => $response->body()
                    ]);
                }

            } catch (\Exception $e) {
                $this->healthChecks[$server->id] = [
                    'healthy' => false,
                    'last_check' => now(),
                    'error' => $e->getMessage()
                ];

                Log::error("Primary server health check error", [
                    'server_id' => $server->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    protected function updateBackupServers()
    {
        foreach ($this->backupServers as $backup) {
            try {
                // Sync configuration from primary
                $primary = $this->getPrimaryForBackup($backup);
                if (!$primary) continue;

                $backup->update([
                    'configuration' => $primary->configuration,
                    'last_sync_at' => now()
                ]);

                // Warm up backup server
                $this->warmupBackupServer($backup);

            } catch (\Exception $e) {
                Log::error("Backup server update failed", [
                    'backup_id' => $backup->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    protected function handleFailovers()
    {
        foreach ($this->healthChecks as $serverId => $health) {
            if ($health['healthy']) continue;

            $primary = $this->primaryServers->find($serverId);
            if (!$primary) continue;

            // Check if server has been unhealthy for more than 5 minutes
            $unhealthyDuration = Cache::get("server.{$serverId}.unhealthy_since");
            if (!$unhealthyDuration) {
                Cache::put("server.{$serverId}.unhealthy_since", now());
                continue;
            }

            if (now()->diffInMinutes($unhealthyDuration) < 5) continue;

            // Initiate failover
            $this->initiateFailover($primary);
        }
    }

    protected function initiateFailover($primary)
    {
        Log::alert("Initiating failover for primary server", [
            'server_id' => $primary->id
        ]);

        try {
            // Find suitable backup server
            $backup = $this->findSuitableBackup($primary);
            if (!$backup) {
                throw new \Exception("No suitable backup server found");
            }

            // Update server statuses
            $primary->update(['status' => 'failed']);
            $backup->update(['status' => 'active']);

            // Update load balancer configuration
            app(LoadBalancerService::class)->updateServerPool();

            // Notify administrators
            // event(new ServerFailoverEvent($primary, $backup));

            Log::info("Failover completed successfully", [
                'primary_id' => $primary->id,
                'backup_id' => $backup->id
            ]);

        } catch (\Exception $e) {
            Log::error("Failover failed", [
                'primary_id' => $primary->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function findSuitableBackup($primary)
    {
        return $this->backupServers
            ->where('status', 'standby')
            ->where('last_sync_at', '>', now()->subMinutes(15))
            ->sortByDesc('last_health_check')
            ->first();
    }

    protected function warmupBackupServer($backup)
    {
        try {
            // Load essential models/data into memory
            Http::timeout(30)
                ->post("{$backup->host}:{$backup->port}/warmup", [
                    'models' => config('ai.models'),
                    'cache_key' => 'backup_warmup'
                ]);

            $backup->update([
                'last_warmup_at' => now()
            ]);

        } catch (\Exception $e) {
            Log::warning("Backup server warmup failed", [
                'backup_id' => $backup->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function getPrimaryForBackup($backup)
    {
        return $this->primaryServers
            ->where('status', 'active')
            ->first();
    }
} 