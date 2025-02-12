<?php

namespace App\Services;

use App\Models\VisitorAnalytic;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Cache;
use App\Models\VisitorSession;

class VisitorTrackingService
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    public function trackVisit($request)
    {
        $ipAddress = $request->ip();
        
        // Get location data from IP-API
        $locationData = $this->getLocationData($ipAddress);
        
        // Create visitor record
        return VisitorAnalytic::create([
            'ip_address' => $ipAddress,
            'user_agent' => $request->userAgent(),
            'country' => $locationData['country'] ?? null,
            'country_code' => $locationData['countryCode'] ?? null,
            'region' => $locationData['regionName'] ?? null,
            'city' => $locationData['city'] ?? null,
            'latitude' => $locationData['lat'] ?? null,
            'longitude' => $locationData['lon'] ?? null,
            'timezone' => $locationData['timezone'] ?? null,
            'isp' => $locationData['isp'] ?? null,
            'page_url' => $request->fullUrl(),
            'referrer_url' => $request->header('referer'),
            'device_type' => $this->getDeviceType(),
            'browser' => $this->agent->browser(),
            'os' => $this->agent->platform(),
            'user_id' => auth()->id(),
            'visited_at' => now(),
        ]);
    }

    protected function getLocationData($ip)
    {
        // Cache location data for 24 hours to avoid API rate limits
        return Cache::remember("ip_data_{$ip}", 60*60*24, function() use ($ip) {
            $response = Http::get("http://ip-api.com/json/{$ip}");
            return $response->json();
        });
    }

    protected function getDeviceType()
    {
        if ($this->agent->isTablet()) {
            return 'tablet';
        } elseif ($this->agent->isMobile()) {
            return 'mobile';
        }
        return 'desktop';
    }

    public function getAnalytics($period = 'today')
    {
        $query = VisitorAnalytic::query();

        switch ($period) {
            case 'today':
                $query->whereDate('visited_at', today());
                break;
            case 'week':
                $query->whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('visited_at', now()->month);
                break;
            case 'year':
                $query->whereYear('visited_at', now()->year);
                break;
        }

        return [
            'total_visitors' => $query->count(),
            'unique_visitors' => $query->distinct('ip_address')->count(),
            'countries' => $query->groupBy('country_code')
                ->selectRaw('country_code, country, count(*) as count')
                ->get(),
            'devices' => $query->groupBy('device_type')
                ->selectRaw('device_type, count(*) as count')
                ->get(),
            'browsers' => $query->groupBy('browser')
                ->selectRaw('browser, count(*) as count')
                ->get(),
            'map_data' => $query->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get(['latitude', 'longitude', 'city', 'country'])
        ];
    }

    public function getRealTimeData($timeRange = 30)
    {
        $since = now()->subMinutes($timeRange);
        
        $visitors = VisitorAnalytic::where('visited_at', '>=', $since)
            ->whereNotNull(['latitude', 'longitude'])
            ->with('session')
            ->get()
            ->map(function ($visitor) {
                return [
                    'id' => $visitor->id,
                    'latitude' => $visitor->latitude,
                    'longitude' => $visitor->longitude,
                    'city' => $visitor->city,
                    'country' => $visitor->country,
                    'page_url' => $visitor->page_url,
                    'duration' => $visitor->session ? $visitor->session->duration : 0
                ];
            });

        $activities = VisitorAnalytic::where('visited_at', '>=', $since)
            ->orderBy('visited_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($visitor) {
                return [
                    'id' => $visitor->id,
                    'type' => 'pageview',
                    'location' => "{$visitor->city}, {$visitor->country}",
                    'action' => "visited {$visitor->page_url}",
                    'timestamp' => $visitor->visited_at
                ];
            });

        $stats = [
            'pageViews' => $this->getPageViewsPerMinute(),
            'sessions' => $this->getActiveSessions(),
            'bounceRate' => $this->getBounceRate()
        ];

        return [
            'visitors' => $visitors,
            'activities' => $activities,
            'activeVisitors' => $this->getActiveVisitors(),
            'stats' => $stats,
            'topCountries' => $this->getTopCountries($timeRange),
            'topPages' => $this->getTopPages($timeRange)
        ];
    }

    protected function getPageViewsPerMinute()
    {
        return VisitorAnalytic::where('visited_at', '>=', now()->subMinutes(1))->count();
    }

    protected function getActiveSessions()
    {
        return VisitorSession::where('ended_at', null)->count();
    }

    protected function getBounceRate()
    {
        $total = VisitorSession::where('ended_at', '>=', now()->subHour())->count();
        if (!$total) return 0;
        
        $bounced = VisitorSession::where('ended_at', '>=', now()->subHour())
            ->where('page_views', 1)
            ->count();
        
        return round(($bounced / $total) * 100);
    }

    protected function getActiveVisitors()
    {
        return VisitorAnalytic::where('visited_at', '>=', now()->subMinutes(5))->count();
    }
} 