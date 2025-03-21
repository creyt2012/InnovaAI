<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::latest()->paginate(10);
        return view('admin.reports.index', compact('reports'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:usage,performance,financial,user',
            'date_range' => 'required|array',
            'filters' => 'nullable|array'
        ]);

        $report = Report::generate($validated);
        return response()->download($report->path);
    }

    public function schedule(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'frequency' => 'required|in:daily,weekly,monthly',
            'recipients' => 'required|array',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        Report::schedule($validated);
        return back()->with('success', 'Report scheduled successfully');
    }
} 