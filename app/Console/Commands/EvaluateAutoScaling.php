<?php

namespace App\Console\Commands;

use App\Services\AutoScalingService;
use Illuminate\Console\Command;

class EvaluateAutoScaling extends Command
{
    protected $signature = 'scaling:evaluate';
    protected $description = 'Evaluate and execute auto-scaling rules';

    protected $scalingService;

    public function __construct(AutoScalingService $scalingService)
    {
        parent::__construct();
        $this->scalingService = $scalingService;
    }

    public function handle()
    {
        $this->info('Evaluating auto-scaling rules...');
        $this->scalingService->evaluate();
        $this->info('Auto-scaling evaluation completed');
    }
} 