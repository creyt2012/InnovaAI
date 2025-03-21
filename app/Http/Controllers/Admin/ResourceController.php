<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::with('usage')->latest()->paginate(10);
        return view('admin.resources.index', compact('resources'));
    }

    public function monitor()
    {
        $metrics = [
            'cpu' => Resource::getCpuUsage(),
            'memory' => Resource::getMemoryUsage(),
            'storage' => Resource::getStorageUsage(),
            'network' => Resource::getNetworkMetrics()
        ];

        return view('admin.resources.monitor', compact('metrics'));
    }

    public function optimize()
    {
        try {
            Resource::optimizeSystem();
            return back()->with('success', 'System optimized successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
} 