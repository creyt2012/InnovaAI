<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PluginDevelopmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ZipArchive;

class PluginDevelopmentController extends Controller
{
    protected $developmentService;

    public function __construct(PluginDevelopmentService $developmentService)
    {
        $this->developmentService = $developmentService;
    }

    public function index()
    {
        return view('admin.plugins.development.index');
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-z0-9-]+$/',
            'description' => 'required|string',
            'author' => 'required|string',
        ]);

        $pluginPath = $this->developmentService->createPluginStructure($validated['name']);

        return response()->json([
            'message' => 'Plugin structure created successfully',
            'path' => $pluginPath
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'plugin' => 'required|file|mimes:zip'
        ]);

        $file = $request->file('plugin');
        $tempPath = storage_path('app/temp/plugins/' . Str::random());
        mkdir($tempPath, 0755, true);

        $zip = new ZipArchive;
        $zip->open($file->path());
        $zip->extractTo($tempPath);
        $zip->close();

        // Validate plugin
        $validation = $this->developmentService->validatePlugin($tempPath);
        if (!$validation['valid']) {
            return back()->withErrors($validation['errors']);
        }

        // Move to plugins directory
        $config = json_decode(file_get_contents($tempPath . '/config.json'), true);
        $pluginName = $config['name'];
        $targetPath = base_path("plugins/{$pluginName}");

        if (file_exists($targetPath)) {
            return back()->withErrors('Plugin already exists');
        }

        rename($tempPath, $targetPath);

        return back()->with('success', 'Plugin uploaded successfully');
    }

    public function documentation()
    {
        return view('admin.plugins.development.documentation');
    }
} 