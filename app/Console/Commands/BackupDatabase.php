<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup database to storage';

    public function handle()
    {
        $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $command = sprintf(
            'mysqldump -u%s -p%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            storage_path('app/backups/' . $filename)
        );

        exec($command);
        $this->info('Database backed up successfully');
    }
} 