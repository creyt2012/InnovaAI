<?php

namespace App\Services;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;

class PythonExecutionService
{
    protected $pythonPath;
    protected $scriptPath;

    public function __construct()
    {
        $this->pythonPath = config('services.python.path', 'python3');
        $this->scriptPath = storage_path('app/python');
    }

    public function generateMathSolver($problem)
    {
        // Tạo code Python dựa trên yêu cầu
        $code = $this->generatePythonCode($problem);
        
        // Lưu vào file tạm
        $filename = 'math_solver_' . time() . '.py';
        Storage::put("python/{$filename}", $code);

        return $filename;
    }

    protected function generatePythonCode($problem)
    {
        // Template cơ bản cho giải toán
        $code = "import math\nimport numpy as np\n\n";
        
        // Thêm các thư viện cần thiết
        if (str_contains($problem, 'matrix')) {
            $code .= "from numpy.linalg import *\n";
        }
        if (str_contains($problem, 'plot')) {
            $code .= "import matplotlib.pyplot as plt\n";
        }
        if (str_contains($problem, 'symbolic')) {
            $code .= "from sympy import *\n";
        }

        // Thêm hàm giải quyết vấn đề
        $code .= "\ndef solve_problem():\n";
        $code .= $this->generateSolutionCode($problem);
        
        return $code;
    }

    public function execute($filename, $input = null)
    {
        $process = new Process([
            $this->pythonPath,
            "{$this->scriptPath}/{$filename}"
        ]);

        if ($input) {
            $process->setInput($input);
        }

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return $process->getOutput();
    }

    protected function generateSolutionCode($problem)
    {
        // Phân tích yêu cầu và tạo code tương ứng
        if (str_contains($problem, 'equation')) {
            return $this->generateEquationSolver($problem);
        }
        if (str_contains($problem, 'matrix')) {
            return $this->generateMatrixSolver($problem);
        }
        if (str_contains($problem, 'calculus')) {
            return $this->generateCalculusSolver($problem);
        }
        // Thêm các loại bài toán khác...
    }

    protected function generateEquationSolver($problem)
    {
        return "
    # Giải phương trình
    x = Symbol('x')
    equation = {$this->parseEquation($problem)}
    solution = solve(equation, x)
    return f'Solution: {solution}'
        ";
    }

    protected function generateMatrixSolver($problem)
    {
        return "
    # Xử lý ma trận
    A = np.array({$this->parseMatrix($problem)})
    if 'inverse' in problem:
        return np.linalg.inv(A)
    elif 'eigenvalues' in problem:
        return np.linalg.eigvals(A)
    else:
        return A
        ";
    }
} 