<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConversionGoal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Analytics/Conversions', [
            'goals' => ConversionGoal::with('conversions')
                ->withCount('conversions')
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pageview,event,custom',
            'target' => 'required|string',
            'conditions' => 'nullable|array',
            'value' => 'nullable|numeric'
        ]);

        ConversionGoal::create($validated);

        return redirect()->back();
    }
} 