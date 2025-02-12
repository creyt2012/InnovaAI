<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataRetentionService;

class CleanupAnalyticsData extends Command
{
    protected $signature = 'analytics:cleanup';
    protected $description = 'Clean up old analytics data';

    public function handle(DataRetentionService $service)
    {
        $this->info('Cleaning up old analytics data...');
        $service->cleanupOldData();
        $this->info('Done!');
    }
} 