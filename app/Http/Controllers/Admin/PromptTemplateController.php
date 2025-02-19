<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromptTemplate;
use Illuminate\Http\Request;

class PromptTemplateController extends Controller
{
    public function index()
    {
        $templates = PromptTemplate::paginate(10);
        return view('admin.prompts.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.prompts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'parameters' => 'nullable|array'
        ]);

        PromptTemplate::create($validated);

        return redirect()->route('admin.prompts.index')
            ->with('success', 'Template created successfully');
    }

    public function edit(PromptTemplate $template)
    {
        return view('admin.prompts.edit', compact('template'));
    }

    public function update(Request $request, PromptTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'parameters' => 'nullable|array'
        ]);

        $template->update($validated);

        return redirect()->route('admin.prompts.index')
            ->with('success', 'Template updated successfully');
    }

    public function destroy(PromptTemplate $template)
    {
        $template->delete();
        return redirect()->route('admin.prompts.index')
            ->with('success', 'Template deleted successfully');
    }
} 