<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class OCRService
{
    public function extractText($file)
    {
        $path = Storage::disk('local')->path($file);
        return (new TesseractOCR($path))
            ->lang('vie', 'eng')
            ->run();
    }
} 