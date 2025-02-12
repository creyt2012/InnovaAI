<?php

namespace App\Services;

use App\Models\VisitorAnalytic;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AnalyticsExport;
use Illuminate\Support\Facades\Storage;
use PDF;

class AnalyticsExportService
{
    public function exportToExcel($filters)
    {
        return Excel::download(
            new AnalyticsExport($filters),
            'analytics-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportToPDF($data)
    {
        $pdf = PDF::loadView('exports.analytics', $data);
        return $pdf->download('analytics-' . now()->format('Y-m-d') . '.pdf');
    }

    public function generateScheduledReport($type, $period)
    {
        $data = $this->gatherReportData($period);
        $path = "reports/{$type}/{$period}/";
        
        switch ($type) {
            case 'excel':
                $file = Excel::store(new AnalyticsExport($data), $path);
                break;
            case 'pdf':
                $file = PDF::loadView('exports.analytics', $data)
                    ->save(storage_path("app/{$path}"));
                break;
        }

        return $path;
    }

    protected function gatherReportData($period)
    {
        // Gather analytics data based on period
    }
} 