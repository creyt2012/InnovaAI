<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compliance;
use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    public function index()
    {
        $requirements = Compliance::getRequirements();
        $status = Compliance::getComplianceStatus();
        return view('admin.compliance.index', compact('requirements', 'status'));
    }

    public function audit()
    {
        $audit = Compliance::runAudit();
        $violations = Compliance::getViolations();
        $recommendations = Compliance::getRecommendations();
        
        return view('admin.compliance.audit', compact('audit', 'violations', 'recommendations'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type', 'full');
        $report = Compliance::generateReport($type);
        return response()->download($report->path);
    }
} 