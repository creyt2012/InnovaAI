<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MonitoringController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Monitoring/Index', [
            'servers' => Server::with('latestMetrics')->get(),
            'stats' => $this->getStats()
        ]);
    }

    public function stats()
    {
        return response()->json($this->getStats());
    }

    protected function getStats()
    {
        return [
            'total_servers' => Server::count(),
            'active_servers' => Server::where('status', 'active')->count(),
            'active_users' => User::where('last_active_at', '>', now()->subMinutes(5))->count(),
            'total_chats' => Chat::count(),
            'server_metrics' => Server::with('latestMetrics')
                ->get()
                ->map(function ($server) {
                    return [
                        'name' => $server->name,
                        'metrics' => $server->latestMetrics
                    ];
                })
        ];
    }
} 