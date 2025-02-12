<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromptTemplate;
use Illuminate\Http\Request;

class PromptTemplateController extends Controller
{
    public function index()
    {
        $templates = PromptTemplate::orderBy('name')->get();
        return view('admin.prompts.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required|string',
            'category' => 'required|string',
            'is_active' => 'boolean'
        ]);

        PromptTemplate::create($validated);
        return back()->with('success', 'Template đã được tạo');
    }
} 