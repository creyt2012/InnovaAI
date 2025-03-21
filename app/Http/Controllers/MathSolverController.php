<?php

namespace App\Http\Controllers;

use App\Services\PythonExecutionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MathSolverController extends Controller
{
    protected $pythonService;

    public function __construct(PythonExecutionService $pythonService)
    {
        $this->pythonService = $pythonService;
    }

    public function solve(Request $request)
    {
        $request->validate([
            'problem' => 'required|string',
            'type' => 'required|in:equation,matrix,calculus,statistics'
        ]);

        try {
            // Tạo file Python
            $filename = $this->pythonService->generateMathSolver(
                $request->problem
            );

            // Thực thi và lấy kết quả
            $result = $this->pythonService->execute($filename);

            return response()->json([
                'success' => true,
                'result' => $result,
                'code' => Storage::get("python/{$filename}")
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function preview(Request $request)
    {
        $request->validate([
            'problem' => 'required|string'
        ]);

        $filename = $this->pythonService->generateMathSolver(
            $request->problem
        );

        return response()->json([
            'code' => Storage::get("python/{$filename}")
        ]);
    }
} 