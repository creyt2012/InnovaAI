<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function index()
    {
        $integrations = Integration::latest()->paginate(10);
        return view('admin.integrations.index', compact('integrations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'provider' => 'required|string',
            'credentials' => 'required|array',
            'settings' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        Integration::create($validated);
        return back()->with('success', 'Integration added successfully');
    }

    public function test(Integration $integration)
    {
        try {
            $result = $integration->testConnection();
            return response()->json([
                'status' => 'success',
                'message' => 'Connection successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
} 