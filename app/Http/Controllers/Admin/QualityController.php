<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QualityCheck;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    public function index()
    {
        $metrics = QualityCheck::getMetrics();
        $issues = QualityCheck::getPendingIssues();
        return view('admin.quality.index', compact('metrics', 'issues'));
    }

    public function monitor()
    {
        $responses = QualityCheck::monitorResponses();
        $accuracy = QualityCheck::calculateAccuracy();
        $satisfaction = QualityCheck::getUserSatisfaction();
        
        return view('admin.quality.monitor', compact('responses', 'accuracy', 'satisfaction'));
    }

    public function review(Request $request, QualityCheck $check)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'feedback' => 'required|string',
            'action_items' => 'nullable|array'
        ]);

        $check->review($validated);
        return back()->with('success', 'Quality check reviewed');
    }
} 