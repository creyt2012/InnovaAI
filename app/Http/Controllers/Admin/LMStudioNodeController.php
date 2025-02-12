<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LMStudioNode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LMStudioNodeController extends Controller
{
    public function index()
    {
        $nodes = LMStudioNode::orderBy('priority')->get();
        return view('admin.lmstudio.nodes.index', compact('nodes'));
    }

    public function create()
    {
        return view('admin.lmstudio.nodes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'max_tokens' => 'integer|min:1',
            'temperature' => 'numeric|between:0,1',
            'timeout' => 'integer|min:1'
        ]);

        LMStudioNode::create($validated);
        Cache::tags('lmstudio_nodes')->flush();

        return redirect()->route('admin.lmstudio.nodes.index')
            ->with('success', 'Node đã được thêm thành công');
    }

    public function edit(LMStudioNode $node)
    {
        return view('admin.lmstudio.nodes.edit', compact('node'));
    }

    public function update(Request $request, LMStudioNode $node)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'max_tokens' => 'integer|min:1',
            'temperature' => 'numeric|between:0,1',
            'timeout' => 'integer|min:1'
        ]);

        $node->update($validated);
        Cache::tags('lmstudio_nodes')->flush();

        return redirect()->route('admin.lmstudio.nodes.index')
            ->with('success', 'Node đã được cập nhật thành công');
    }

    public function destroy(LMStudioNode $node)
    {
        $node->delete();
        Cache::tags('lmstudio_nodes')->flush();

        return redirect()->route('admin.lmstudio.nodes.index')
            ->with('success', 'Node đã được xóa thành công');
    }

    public function test(LMStudioNode $node)
    {
        try {
            $response = Http::timeout($node->timeout)->get($node->url . '/health');
            $isHealthy = $response->successful();

            return response()->json([
                'status' => $isHealthy ? 'success' : 'error',
                'message' => $isHealthy ? 'Node hoạt động bình thường' : 'Node không phản hồi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không thể kết nối tới node: ' . $e->getMessage()
            ], 500);
        }
    }
} 