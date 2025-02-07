<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QueryLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Rap2hpoutre\FastExcel\FastExcel;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = QueryLog::with('user')
            ->when($request->search, function ($query, $search) {
                $query->where('query', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_range, function ($query, $dateRange) {
                $query->whereBetween('created_at', $dateRange);
            });

        return Inertia::render('Admin/Logs/Index', [
            'logs' => $query->latest()->paginate(15),
            'filters' => $request->only(['search', 'status', 'date_range']),
        ]);
    }

    public function export(Request $request)
    {
        $logs = QueryLog::with('user')
            ->when($request->date_range, function ($query, $dateRange) {
                $query->whereBetween('created_at', $dateRange);
            })
            ->get();

        return (new FastExcel($logs))->download('query-logs.xlsx', function ($log) {
            return [
                'ID' => $log->id,
                'User' => $log->user->name,
                'Query' => $log->query,
                'Response' => $log->response,
                'Status' => $log->status,
                'Processing Time' => $log->processing_time . 'ms',
                'Created At' => $log->created_at->format('Y-m-d H:i:s'),
            ];
        });
    }
} 