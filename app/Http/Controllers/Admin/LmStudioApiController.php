<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmStudioApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LmStudioApiController extends Controller
{
    public function index()
    {
        $apis = LmStudioApi::latest()->paginate(10);
        return view('admin.apis.index', compact('apis'));
    }

    public function create()
    {
        return view('admin.apis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'endpoint' => 'required|url',
            'api_key' => 'required|string',
            'model' => 'required|string',
            'max_tokens' => 'required|integer|min:1',
            'temperature' => 'required|numeric|between:0,2',
            'status' => 'required|in:active,inactive',
            'priority' => 'required|integer|min:0',
            'rate_limit' => 'required|integer|min:1',
            'timeout' => 'required|integer|min:1',
        ]);

        LmStudioApi::create($validated);

        return redirect()->route('admin.apis.index')
            ->with('success', 'API endpoint added successfully');
    }

    public function edit(LmStudioApi $api)
    {
        return view('admin.apis.edit', compact('api'));
    }

    public function update(Request $request, LmStudioApi $api)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'endpoint' => 'required|url',
            'api_key' => 'required|string',
            'model' => 'required|string',
            'max_tokens' => 'required|integer|min:1',
            'temperature' => 'required|numeric|between:0,2',
            'status' => 'required|in:active,inactive',
            'priority' => 'required|integer|min:0',
            'rate_limit' => 'required|integer|min:1',
            'timeout' => 'required|integer|min:1',
        ]);

        $api->update($validated);

        return redirect()->route('admin.apis.index')
            ->with('success', 'API endpoint updated successfully');
    }

    public function testConnection(LmStudioApi $api)
    {
        try {
            $response = Http::timeout($api->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $api->api_key,
                    'Content-Type' => 'application/json',
                ])
                ->post($api->endpoint, [
                    'model' => $api->model,
                    'messages' => [
                        ['role' => 'user', 'content' => 'Test connection']
                    ],
                    'max_tokens' => 10,
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                $api->update([
                    'last_check' => now(),
                    'status' => 'active'
                ]);
                return response()->json(['success' => true, 'message' => 'Connection successful']);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Connection failed: ' . $response->body()
            ], 400);

        } catch (\Exception $e) {
            $api->update([
                'last_check' => now(),
                'status' => 'inactive'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(LmStudioApi $api)
    {
        $api->delete();
        return redirect()->route('admin.apis.index')
            ->with('success', 'API endpoint deleted successfully');
    }
} 