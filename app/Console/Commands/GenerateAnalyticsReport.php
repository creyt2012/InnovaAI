<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateAnalyticsReport extends Command
{
    protected $signature = 'analytics:report {type} {--email=}';
    
    public function handle()
    {
        // Generate and send report
    }
} 