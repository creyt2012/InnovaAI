<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\AnalyticsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsExportController extends Controller
{
    public function export(Request $request)
    {
        return Excel::download(
            new AnalyticsExport($request->all()),
            'analytics-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
} 