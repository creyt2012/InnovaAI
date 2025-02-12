<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\PredictiveScalingService;
use App\Services\CostOptimizationService;
use App\Services\BackupServerService;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These schedules are run in a default, single-server configuration.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('servers:collect-metrics')->everyMinute();
        $schedule->command('scaling:evaluate')->everyFiveMinutes();

        $schedule->call(function () {
            app(PredictiveScalingService::class)->analyze();
        })->hourly();

        $schedule->call(function () {
            app(CostOptimizationService::class)->analyze();
        })->daily();

        $schedule->call(function () {
            app(BackupServerService::class)->monitor();
        })->everyFiveMinutes();

        // Daily cleanup
        $schedule->command('analytics:cleanup')->daily();

        // Daily report
        $schedule->command('analytics:report daily')->dailyAt('08:00');

        // Weekly report
        $schedule->command('analytics:report weekly')->weekly()->mondays()->at('08:00');

        // Monthly report
        $schedule->command('analytics:report monthly')->monthly()->at('08:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 