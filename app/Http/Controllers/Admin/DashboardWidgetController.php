<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardWidget;
use Illuminate\Http\Request;

class DashboardWidgetController extends Controller
{
    public function index()
    {
        $widgets = DashboardWidget::with('user')->get();
        return view('admin.widgets.index', compact('widgets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:stats,chart,list,custom',
            'title' => 'required|string',
            'data_source' => 'required|string',
            'refresh_interval' => 'required|integer',
            'size' => 'required|in:small,medium,large',
            'position' => 'required|array'
        ]);

        auth()->user()->widgets()->create($validated);
        return back()->with('success', 'Widget added');
    }

    public function layout(Request $request)
    {
        $request->validate([
            'layout' => 'required|array',
            'layout.*.id' => 'required|exists:dashboard_widgets,id',
            'layout.*.position' => 'required|array'
        ]);

        foreach ($request->layout as $widget) {
            DashboardWidget::find($widget['id'])
                ->update(['position' => $widget['position']]);
        }

        return response()->json(['success' => true]);
    }
} 