<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_chats' => Chat::count(),
            'daily_active_users' => $this->getDailyActiveUsers(),
            'average_response_time' => $this->getAverageResponseTime(),
            'popular_topics' => $this->getPopularTopics(),
            'node_performance' => $this->getNodePerformance()
        ];

        return view('admin.analytics.index', compact('stats'));
    }
} 