<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QueryLog;
use App\Models\Server;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('admin.dashboard.stats', 60, function () {
            return [
                'total_queries' => QueryLog::count(),
                'active_servers' => Server::where('status', 'active')->count(),
                'total_users' => User::count(),
                'recent_queries' => QueryLog::with('user')
                    ->latest()
                    ->take(5)
                    ->get(),
                'server_stats' => $this->getServerStats(),
                'hourly_queries' => $this->getHourlyQueryStats(),
            ];
        });

        return Inertia::render('Admin/Dashboard', $stats);
    }

    private function getServerStats()
    {
        return Server::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
    }

    private function getHourlyQueryStats()
    {
        return QueryLog::select(
            \DB::raw('HOUR(created_at) as hour'),
            \DB::raw('COUNT(*) as count')
        )
            ->whereDate('created_at', today())
            ->groupBy('hour')
            ->get()
            ->pluck('count', 'hour');
    }
} 