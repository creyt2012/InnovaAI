<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function index()
    {
        $apiKeys = ApiKey::with('user')->latest()->get();
        return view('admin.api-keys.index', compact('apiKeys'));
    }

    public function generate(Request $request)
    {
        $apiKey = ApiKey::create([
            'user_id' => $request->user_id,
            'key' => Str::random(32),
            'expires_at' => now()->addYear()
        ]);

        return back()->with('success', 'API key đã được tạo');
    }
} 