<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PDF;
use App\Services\ChatExportService;
use App\Models\User;
use App\Notifications\ChatExportCompleted;

class ProcessChatExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $chatId;
    protected $format;

    public function __construct($userId, $chatId, $format = 'pdf')
    {
        $this->userId = $userId;
        $this->chatId = $chatId;
        $this->format = $format;
    }

    public function handle()
    {
        $chatExportService = app(ChatExportService::class);
        
        $url = match($this->format) {
            'pdf' => $chatExportService->exportToPdf($this->chatId, $this->userId),
            'docx' => $chatExportService->exportToWord($this->chatId, $this->userId),
            default => throw new \Exception('Unsupported format')
        };

        // Notify user that export is complete
        $user = User::find($this->userId);
        $user->notify(new ChatExportCompleted($url));
    }
} 