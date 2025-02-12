<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Services\PluginMarketplaceService;
use Illuminate\Http\Request;

class PluginManagementController extends Controller
{
    protected $pluginService;

    public function __construct(PluginMarketplaceService $pluginService)
    {
        $this->pluginService = $pluginService;
    }

    public function index()
    {
        $plugins = Plugin::with(['users'])->get();
        $availablePlugins = $this->pluginService->listAvailablePlugins();

        return view('admin.plugins.index', compact('plugins', 'availablePlugins'));
    }

    public function updateStatus(Request $request, Plugin $plugin)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,pending'
        ]);

        $plugin->update($validated);

        return back()->with('success', 'Plugin status updated successfully');
    }

    public function updateSettings(Request $request, Plugin $plugin)
    {
        $validated = $request->validate([
            'settings' => 'required|array'
        ]);

        $plugin->update($validated);

        return back()->with('success', 'Plugin settings updated successfully');
    }

    public function analytics(Plugin $plugin)
    {
        $usageStats = [
            'total_installations' => $plugin->users()->count(),
            'active_users' => $plugin->users()->wherePivot('activated_at', '!=', null)->count(),
            'average_usage_time' => '2.5 hours', // Implement actual calculation
            'popular_features' => [
                'feature1' => 150,
                'feature2' => 120
            ]
        ];

        return view('admin.plugins.analytics', compact('plugin', 'usageStats'));
    }

    public function permissions(Request $request, Plugin $plugin)
    {
        $validated = $request->validate([
            'permissions' => 'required|array'
        ]);

        $plugin->update($validated);

        return back()->with('success', 'Plugin permissions updated successfully');
    }
} 