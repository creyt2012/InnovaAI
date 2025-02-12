<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AnalyticsExportService;
use App\Services\NotificationService;

class SendAnalyticsReport extends Command
{
    protected $signature = 'analytics:report {type=excel} {period=daily}';
    protected $description = 'Generate and send analytics report';

    public function handle(AnalyticsExportService $exportService, NotificationService $notificationService)
    {
        $this->info('Generating report...');
        
        $report = $exportService->generateScheduledReport(
            $this->argument('type'),
            $this->argument('period')
        );

        $this->info('Sending report...');
        
        $notificationService->sendScheduledReport(
            $report,
            $this->getRecipients()
        );

        $this->info('Done!');
    }

    protected function getRecipients()
    {
        return config('analytics.report_recipients', []);
    }
} 