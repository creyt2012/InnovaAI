<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $files = Storage::disk('public')->files('uploads');
        return view('admin.media.index', compact('files'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240'
        ]);

        $path = $request->file('file')->store('uploads', 'public');
        return back()->with('success', 'File đã được tải lên');
    }
} 