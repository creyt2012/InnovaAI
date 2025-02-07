<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\AiModel;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::with('latestMetrics')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $servers
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'configuration' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $server = Server::create($validator->validated());

        try {
            // Quét models từ LMStudio server
            $response = Http::get($server->url . '/v1/models');
            $models = $response->json()['data'] ?? [];

            foreach ($models as $model) {
                AiModel::create([
                    'server_id' => $server->id,
                    'name' => $model['name'] ?? basename($model['path']),
                    'path' => $model['path'],
                    'category' => $this->detectModelCategory($model['path']),
                    'parameters' => [
                        'type' => $model['type'] ?? 'unknown',
                        'quantization' => $model['quantization'] ?? null,
                    ],
                    'context_length' => $model['context_length'] ?? 4096,
                ]);
            }
        } catch (\Exception $e) {
            // Log error nhưng không dừng việc tạo server
            \Log::error('Failed to fetch models: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data' => $server
        ], 201);
    }

    private function detectModelCategory($path)
    {
        $name = strtolower(basename($path));
        
        if (str_contains($name, 'chat')) return 'Chat';
        if (str_contains($name, 'code')) return 'Code';
        if (str_contains($name, 'instruct')) return 'Instruct';
        
        return 'Base';
    }

    public function show($id)
    {
        $server = Server::with(['metrics' => function ($query) {
            $query->latest()->take(24); // Lấy 24 metrics gần nhất
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $server
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'configuration' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $server = Server::findOrFail($id);
        $server->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $server
        ]);
    }

    public function destroy($id)
    {
        $server = Server::findOrFail($id);
        $server->delete();

        return response()->json([
            'success' => true,
            'message' => 'Server deleted successfully'
        ]);
    }

    public function metrics(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'cpu_usage' => 'required|numeric|min:0|max:100',
            'memory_usage' => 'required|numeric|min:0|max:100',
            'disk_usage' => 'required|numeric|min:0|max:100',
            'active_connections' => 'required|integer|min:0',
            'requests_per_minute' => 'required|integer|min:0',
            'response_time' => 'required|numeric|min:0',
            'additional_metrics' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $server = Server::findOrFail($id);
        $metrics = $server->metrics()->create($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $metrics
        ], 201);
    }

    public function test($id)
    {
        $server = Server::findOrFail($id);

        try {
            $response = Http::timeout(5)
                ->get("{$server->host}:{$server->port}/health");

            if ($response->successful()) {
                $server->update([
                    'status' => 'active',
                    'last_ping_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful'
                ]);
            }

            throw new \Exception('Health check failed');

        } catch (\Exception $e) {
            $server->update(['status' => 'error']);

            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ], 500);
        }
    }
} 