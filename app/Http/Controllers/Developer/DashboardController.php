<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $developer = auth()->user()->developer;
        $plugins = $developer->plugins()->withCount(['users', 'downloads'])->get();
        
        $stats = [
            'total_plugins' => $plugins->count(),
            'total_users' => $plugins->sum('users_count'),
            'total_downloads' => $plugins->sum('downloads_count'),
            'total_revenue' => $plugins->sum('revenue')
        ];

        return view('developer.dashboard', compact('plugins', 'stats'));
    }

    public function analytics(Plugin $plugin)
    {
        $this->authorize('view', $plugin);

        $usageStats = [
            'daily_active_users' => $plugin->getDailyActiveUsers(),
            'monthly_active_users' => $plugin->getMonthlyActiveUsers(),
            'usage_by_country' => $plugin->getUsageByCountry(),
            'popular_features' => $plugin->getPopularFeatures(),
            'revenue_stats' => $plugin->getRevenueStats()
        ];

        return view('developer.analytics', compact('plugin', 'usageStats'));
    }
} 