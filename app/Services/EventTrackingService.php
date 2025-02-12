<?php

namespace App\Services;

use App\Models\CustomEvent;
use Illuminate\Support\Facades\Cache;

class EventTrackingService
{
    public function trackEvent($name, $data = [], $userId = null)
    {
        return CustomEvent::create([
            'name' => $name,
            'data' => $data,
            'user_id' => $userId ?? auth()->id(),
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function getEventStats($eventName, $period = 'today')
    {
        return Cache::remember("event_stats_{$eventName}_{$period}", 60, function () use ($eventName, $period) {
            return CustomEvent::where('name', $eventName)
                ->when($period === 'today', fn($q) => $q->whereDate('created_at', today()))
                ->when($period === 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                ->when($period === 'month', fn($q) => $q->whereMonth('created_at', now()->month))
                ->get()
                ->groupBy(fn($event) => $event->created_at->format('Y-m-d'));
        });
    }
} 