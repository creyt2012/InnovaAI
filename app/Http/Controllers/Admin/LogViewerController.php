<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class LogViewerController extends Controller
{
    public function index()
    {
        $logs = collect(File::files(storage_path('logs')))
            ->map(function ($file) {
                return [
                    'filename' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime()
                ];
            });

        return view('admin.logs.index', compact('logs'));
    }

    public function show($filename)
    {
        $path = storage_path("logs/{$filename}");
        $content = File::get($path);
        
        return view('admin.logs.show', compact('content', 'filename'));
    }

    public function download($filename)
    {
        $path = storage_path("logs/{$filename}");
        return response()->download($path);
    }
} 