<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmStudioApi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;

class LmStudioApiController extends Controller
{
    public function index()
    {
        $apis = LmStudioApi::latest()->paginate(10);
        return Inertia::render('Admin/LmStudioApis/Index', [
            'apis' => $apis
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/LmStudioApis/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'endpoint' => 'required|url',
            'api_key' => 'required|string',
            'configuration' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        // Test connection before saving
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $validated['api_key']
            ])->get($validated['endpoint'] . '/v1/models');

            if (!$response->successful()) {
                return back()->withErrors(['endpoint' => 'Could not connect to API endpoint']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['endpoint' => 'Connection error: ' . $e->getMessage()]);
        }

        LmStudioApi::create($validated);

        return redirect()->route('admin.apis.index')
            ->with('success', 'API added successfully');
    }

    public function edit(LmStudioApi $api)
    {
        return Inertia::render('Admin/LmStudioApis/Edit', [
            'api' => $api
        ]);
    }

    public function update(Request $request, LmStudioApi $api)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'endpoint' => 'required|url',
            'api_key' => 'required|string',
            'configuration' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        // Test connection before updating
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $validated['api_key']
            ])->get($validated['endpoint'] . '/v1/models');

            if (!$response->successful()) {
                return back()->withErrors(['endpoint' => 'Could not connect to API endpoint']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['endpoint' => 'Connection error: ' . $e->getMessage()]);
        }

        $api->update($validated);

        return redirect()->route('admin.apis.index')
            ->with('success', 'API updated successfully');
    }

    public function destroy(LmStudioApi $api)
    {
        $api->delete();
        return back()->with('success', 'API deleted successfully');
    }

    public function testConnection(LmStudioApi $api)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $api->api_key
            ])->get($api->endpoint . '/v1/models');

            if ($response->successful()) {
                $models = $response->json();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Connection successful',
                    'models' => $models
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Could not connect to API'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Connection error: ' . $e->getMessage()
            ], 500);
        }
    }
} 