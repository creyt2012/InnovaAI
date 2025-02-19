<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::grouped();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        cache()->forget('system_settings');
        return back()->with('success', 'Settings updated successfully');
    }

    public function clearCache()
    {
        cache()->flush();
        return back()->with('success', 'Cache cleared successfully');
    }
} 