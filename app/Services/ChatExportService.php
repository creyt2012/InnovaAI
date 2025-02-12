<?php

namespace App\Services;

use App\Models\Chat;
use PDF;
use Illuminate\Support\Facades\Storage;

class ChatExportService
{
    public function exportToPdf($chatId, $userId)
    {
        $chat = Chat::with('messages')
            ->where('user_id', $userId)
            ->findOrFail($chatId);

        $pdf = PDF::loadView('exports.chat', [
            'chat' => $chat
        ]);

        $filename = 'chat-' . $chatId . '-' . now()->format('Y-m-d') . '.pdf';
        Storage::put('public/exports/' . $filename, $pdf->output());

        return Storage::url('exports/' . $filename);
    }

    public function exportToWord($chatId, $userId)
    {
        $chat = Chat::with('messages')
            ->where('user_id', $userId)
            ->findOrFail($chatId);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        
        foreach ($chat->messages as $message) {
            $section->addText($message->content);
            $section->addTextBreak(1);
        }

        $filename = 'chat-' . $chatId . '-' . now()->format('Y-m-d') . '.docx';
        $phpWord->save(storage_path('app/public/exports/' . $filename));

        return Storage::url('exports/' . $filename);
    }
} 