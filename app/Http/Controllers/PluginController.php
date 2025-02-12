<?php

namespace App\Http\Controllers;

use App\Models\Plugin;
use App\Services\PluginMarketplaceService;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    protected $pluginService;

    public function __construct(PluginMarketplaceService $pluginService)
    {
        $this->pluginService = $pluginService;
    }

    public function index()
    {
        $plugins = $this->pluginService->listAvailablePlugins();
        $installedPlugins = auth()->user()->plugins;

        return view('plugins.index', compact('plugins', 'installedPlugins'));
    }

    public function install(Request $request, $pluginId)
    {
        try {
            $this->pluginService->installPlugin($pluginId);
            return back()->with('success', 'Plugin installed successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function uninstall(Request $request, $pluginId)
    {
        try {
            $this->pluginService->uninstallPlugin($pluginId);
            return back()->with('success', 'Plugin uninstalled successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function configure(Request $request, $pluginId)
    {
        $plugin = Plugin::findOrFail($pluginId);
        $settings = $request->validate([
            'settings' => 'required|array'
        ]);

        $plugin->update($settings);
        return back()->with('success', 'Plugin settings updated');
    }
} 