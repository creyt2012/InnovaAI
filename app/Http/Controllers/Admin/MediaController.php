<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::latest()->paginate(20);
        return view('admin.media.index', compact('media'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240'
        ]);

        $path = $request->file('file')->store('media');
        
        Media::create([
            'name' => $request->file('file')->getClientOriginalName(),
            'path' => $path,
            'type' => $request->file('file')->getMimeType(),
            'size' => $request->file('file')->getSize(),
            'uploaded_by' => auth()->id()
        ]);

        return back()->with('success', 'File uploaded successfully');
    }
} 