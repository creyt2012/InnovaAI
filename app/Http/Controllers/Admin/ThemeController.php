<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThemeController extends Controller 
{
    public function index()
    {
        return view('admin.theme.index', [
            'currentTheme' => setting('admin.theme'),
            'colors' => setting('admin.colors'),
            'layout' => setting('admin.layout')
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|in:light,dark,system',
            'primary_color' => 'required|string',
            'layout' => 'required|in:sidebar,horizontal,compact'
        ]);

        setting(['admin.theme' => $validated['theme']]);
        setting(['admin.colors' => $validated['primary_color']]);
        setting(['admin.layout' => $validated['layout']]);

        return back()->with('success', 'Admin theme updated');
    }
} 