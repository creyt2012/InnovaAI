<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupSystem extends Command
{
    protected $signature = 'backup:run';
    protected $description = 'Backup system data';

    public function handle()
    {
        // Backup database
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $process = new Process([
            'mysqldump',
            '-u' . config('database.connections.mysql.username'),
            '-p' . config('database.connections.mysql.password'),
            config('database.connections.mysql.database'),
            '>' . storage_path('app/backups/' . $filename)
        ]);

        $process->run();

        // Backup files
        Storage::disk('s3')->put(
            'backups/' . $filename,
            Storage::disk('local')->get('backups/' . $filename)
        );
    }
} 