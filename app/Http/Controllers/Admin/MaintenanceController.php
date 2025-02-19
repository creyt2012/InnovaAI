<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    public function show()
    {
        return view('admin.maintenance.index');
    }

    public function enable(Request $request)
    {
        $message = $request->input('message', 'Site is under maintenance.');
        Artisan::call('down', ['--message' => $message]);
        return back()->with('success', 'Maintenance mode enabled');
    }

    public function disable()
    {
        Artisan::call('up');
        return back()->with('success', 'Maintenance mode disabled');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        return back()->with('success', 'Cache cleared successfully');
    }
} 