<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function index()
    {
        $suspiciousActivities = SecurityLog::suspicious()
                                         ->latest()
                                         ->paginate(20);
                                         
        return view('admin.security.index', compact('suspiciousActivities'));
    }

    public function blockIp(Request $request)
    {
        $this->validate($request, [
            'ip' => 'required|ip'
        ]);

        cache()->forever('blocked_ip:' . $request->ip, true);
        return back()->with('success', 'Đã chặn IP');
    }

    public function auditLog()
    {
        $logs = SecurityLog::with('user')
                          ->latest()
                          ->paginate(50);
                          
        return view('admin.security.audit', compact('logs'));
    }
} 