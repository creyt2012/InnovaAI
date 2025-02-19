<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiEndpoint;
use Illuminate\Http\Request;

class ApiManagementController extends Controller
{
    public function index()
    {
        $endpoints = ApiEndpoint::withCount('requests')
                              ->orderBy('requests_count', 'desc')
                              ->paginate(20);
                              
        return view('admin.api.index', compact('endpoints'));
    }

    public function rateLimit(Request $request, ApiEndpoint $endpoint)
    {
        $endpoint->update([
            'rate_limit' => $request->rate_limit,
            'rate_limit_period' => $request->period
        ]);

        return back()->with('success', 'Đã cập nhật giới hạn tốc độ');
    }

    public function monitor()
    {
        $stats = [
            'total_requests' => ApiEndpoint::sum('requests_count'),
            'average_response_time' => ApiEndpoint::avg('average_response_time'),
            'error_rate' => $this->calculateErrorRate()
        ];

        return view('admin.api.monitor', compact('stats'));
    }
} 