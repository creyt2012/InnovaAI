<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activities = AdminActivity::with('user')
            ->latest()
            ->paginate(20);
            
        return view('admin.activities.index', compact('activities'));
    }

    public function filter(Request $request)
    {
        $activities = AdminActivity::query()
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->date_range, function($q) use ($request) {
                $dates = explode(',', $request->date_range);
                $q->whereBetween('created_at', $dates);
            })
            ->latest()
            ->paginate(20);

        return view('admin.activities.index', compact('activities'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new AdminActivityExport($request->all()),
            'admin-activities.xlsx'
        );
    }
} 