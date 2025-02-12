<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Services\PluginDevelopmentService;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    protected $developmentService;

    public function __construct(PluginDevelopmentService $developmentService)
    {
        $this->developmentService = $developmentService;
    }

    public function index()
    {
        $plugins = auth()->user()->developer->plugins()->latest()->get();
        return view('developer.plugins.index', compact('plugins'));
    }

    public function create()
    {
        return view('developer.plugins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:plugins',
            'description' => 'required|string',
            'version' => 'required|string',
            'price' => 'required|numeric|min:0',
            'plugin_file' => 'required|file|mimes:zip|max:10240'
        ]);

        $plugin = $this->developmentService->uploadPlugin(
            $request->file('plugin_file'),
            auth()->user()->developer
        );

        return redirect()->route('developer.plugins.show', $plugin)
            ->with('success', 'Plugin uploaded successfully');
    }

    public function update(Request $request, Plugin $plugin)
    {
        $this->authorize('update', $plugin);

        $validated = $request->validate([
            'version' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'plugin_file' => 'nullable|file|mimes:zip|max:10240'
        ]);

        if ($request->hasFile('plugin_file')) {
            $this->developmentService->updatePluginFile(
                $plugin,
                $request->file('plugin_file')
            );
        }

        $plugin->update($validated);

        return back()->with('success', 'Plugin updated successfully');
    }
} 