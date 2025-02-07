<?php

namespace App\Services;

use App\Models\User;
use App\Models\Chat;
use App\Models\Report;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use PDF;

class ReportGenerationService
{
    public function generateReport($type, $params = [])
    {
        $method = "generate{$type}Report";
        if (!method_exists($this, $method)) {
            throw new \Exception("Report type not supported");
        }

        $data = $this->$method($params);
        $report = $this->saveReport($type, $data);

        return $this->formatReport($report, $params['format'] ?? 'xlsx');
    }

    protected function generateUsageReport($params)
    {
        $startDate = Carbon::parse($params['start_date']);
        $endDate = Carbon::parse($params['end_date']);

        return [
            'summary' => [
                'total_queries' => Chat::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
                'average_response_time' => Chat::avg('response_time'),
            ],
            'daily_stats' => Chat::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->get(),
            'user_stats' => User::withCount(['chats' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])->get(),
        ];
    }

    protected function generatePerformanceReport($params)
    {
        return [
            'server_metrics' => Server::with('metrics')
                ->get()
                ->map(function ($server) {
                    return [
                        'name' => $server->name,
                        'avg_cpu' => $server->metrics->avg('cpu_usage'),
                        'avg_memory' => $server->metrics->avg('memory_usage'),
                        'avg_response_time' => $server->metrics->avg('response_time'),
                    ];
                }),
            'response_times' => Chat::selectRaw('AVG(response_time) as avg_time, HOUR(created_at) as hour')
                ->groupBy('hour')
                ->get(),
        ];
    }

    protected function generateSecurityReport($params)
    {
        return [
            'login_attempts' => AuditLog::where('action', 'login_attempt')
                ->selectRaw('COUNT(*) as count, success, DATE(created_at) as date')
                ->groupBy('date', 'success')
                ->get(),
            'api_access' => AuditLog::where('action', 'api_access')
                ->selectRaw('COUNT(*) as count, status, endpoint')
                ->groupBy('endpoint', 'status')
                ->get(),
            'alerts' => Alert::where('severity', 'critical')
                ->with('data')
                ->latest()
                ->take(100)
                ->get(),
        ];
    }

    protected function formatReport($report, $format)
    {
        switch ($format) {
            case 'xlsx':
                return $this->generateExcel($report);
            case 'pdf':
                return $this->generatePDF($report);
            case 'json':
                return response()->json($report);
            default:
                throw new \Exception("Format not supported");
        }
    }

    protected function generateExcel($report)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Format data for Excel
        foreach ($report['data'] as $row => $data) {
            foreach ($data as $col => $value) {
                $sheet->setCellValueByColumnAndRow($col + 1, $row + 1, $value);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "report_{$report->id}.xlsx";
        $path = Storage::path("reports/{$filename}");
        $writer->save($path);

        return $path;
    }

    protected function generatePDF($report)
    {
        $pdf = PDF::loadView('reports.template', [
            'report' => $report,
            'generated_at' => now(),
        ]);

        $filename = "report_{$report->id}.pdf";
        $path = Storage::path("reports/{$filename}");
        $pdf->save($path);

        return $path;
    }

    protected function saveReport($type, $data)
    {
        return Report::create([
            'type' => $type,
            'data' => $data,
            'generated_by' => auth()->id(),
            'format' => $format,
            'file_path' => $path,
        ]);
    }
} 