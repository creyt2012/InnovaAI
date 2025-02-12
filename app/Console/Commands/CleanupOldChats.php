<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;

class CleanupOldChats extends Command
{
    protected $signature = 'chats:cleanup {--days=30}';
    protected $description = 'Cleanup old chat records';

    public function handle()
    {
        $days = $this->option('days');
        Chat::where('created_at', '<', now()->subDays($days))->delete();
        $this->info('Old chats cleaned up successfully');
    }
} 