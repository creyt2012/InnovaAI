<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('price')->get();
        return view('admin.packages.index', compact('packages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'features' => 'required|array',
            'max_tokens' => 'required|integer',
            'is_active' => 'boolean'
        ]);

        Package::create($validated);
        return back()->with('success', 'Gói dịch vụ đã được tạo');
    }

    // Thêm các methods khác...
} 