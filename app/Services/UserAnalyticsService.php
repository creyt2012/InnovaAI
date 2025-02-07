<?php

namespace App\Services;

use App\Models\User;
use App\Models\Chat;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class UserAnalyticsService
{
    public function analyze()
    {
        return [
            'engagement' => $this->analyzeEngagement(),
            'retention' => $this->analyzeRetention(),
            'usage_patterns' => $this->analyzeUsagePatterns(),
            'feature_adoption' => $this->analyzeFeatureAdoption(),
        ];
    }

    protected function analyzeEngagement()
    {
        $thirtyDaysAgo = now()->subDays(30);

        return [
            'daily_active_users' => $this->getDailyActiveUsers(),
            'weekly_active_users' => $this->getWeeklyActiveUsers(),
            'monthly_active_users' => $this->getMonthlyActiveUsers(),
            'average_session_duration' => $this->getAverageSessionDuration(),
            'queries_per_user' => Chat::where('created_at', '>=', $thirtyDaysAgo)
                ->selectRaw('user_id, COUNT(*) as query_count')
                ->groupBy('user_id')
                ->avg('query_count'),
        ];
    }

    protected function analyzeRetention()
    {
        $cohorts = [];
        $startDate = now()->subMonths(3)->startOfMonth();

        while ($startDate <= now()) {
            $cohort = User::whereMonth('created_at', $startDate->month)
                ->whereYear('created_at', $startDate->year)
                ->get();

            $retention = [];
            for ($i = 0; $i <= $startDate->diffInMonths(now()); $i++) {
                $activeUsers = $cohort->filter(function ($user) use ($startDate, $i) {
                    return $user->activities()
                        ->whereMonth('created_at', $startDate->copy()->addMonths($i)->month)
                        ->exists();
                })->count();

                $retention[$i] = [
                    'month' => $startDate->copy()->addMonths($i)->format('M Y'),
                    'rate' => $cohort->count() > 0 ? ($activeUsers / $cohort->count()) * 100 : 0
                ];
            }

            $cohorts[] = [
                'cohort_date' => $startDate->format('M Y'),
                'size' => $cohort->count(),
                'retention' => $retention
            ];

            $startDate->addMonth();
        }

        return $cohorts;
    }

    protected function analyzeUsagePatterns()
    {
        return [
            'peak_hours' => $this->getPeakHours(),
            'popular_features' => $this->getPopularFeatures(),
            'device_usage' => $this->getDeviceUsage(),
            'session_frequency' => $this->getSessionFrequency(),
        ];
    }

    protected function analyzeFeatureAdoption()
    {
        $features = [
            'file_upload' => UserActivity::where('action', 'file_upload')->count(),
            'api_usage' => UserActivity::where('action', 'api_access')->count(),
            'dark_mode' => UserActivity::where('action', 'toggle_dark_mode')->count(),
        ];

        $totalUsers = User::count();
        
        return collect($features)->map(function ($count) use ($totalUsers) {
            return [
                'count' => $count,
                'adoption_rate' => ($count / $totalUsers) * 100
            ];
        });
    }

    protected function getPeakHours()
    {
        return UserActivity::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();
    }

    protected function getPopularFeatures()
    {
        return UserActivity::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();
    }

    protected function getDeviceUsage()
    {
        return UserActivity::selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->get();
    }

    protected function getSessionFrequency()
    {
        $users = User::with('activities')->get();
        
        return $users->map(function ($user) {
            $sessions = $this->identifySessions($user->activities);
            return [
                'user_id' => $user->id,
                'average_sessions_per_week' => $sessions->count() / 4, // Last 4 weeks
                'average_session_duration' => $sessions->avg('duration'),
            ];
        });
    }

    protected function identifySessions($activities)
    {
        $sessions = collect();
        $sessionTimeout = 30; // minutes
        
        $activities = $activities->sortBy('created_at');
        $sessionStart = null;
        $lastActivity = null;
        
        foreach ($activities as $activity) {
            if (!$sessionStart) {
                $sessionStart = $activity->created_at;
            } elseif ($activity->created_at->diffInMinutes($lastActivity) > $sessionTimeout) {
                $sessions->push([
                    'start' => $sessionStart,
                    'end' => $lastActivity,
                    'duration' => $sessionStart->diffInMinutes($lastActivity)
                ]);
                $sessionStart = $activity->created_at;
            }
            
            $lastActivity = $activity->created_at;
        }
        
        return $sessions;
    }
} 