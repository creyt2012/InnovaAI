<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return response()->json([
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'hero_title' => 'required|string|max:255',
            'hero_description' => 'required|string',
            'features' => 'required|json'
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/images');
            $logoUrl = Storage::url($path);
            $this->updateSetting('logo', $logoUrl);
        }

        // Update other settings
        $this->updateSetting('app_name', $request->app_name);
        $this->updateSetting('hero_title', $request->hero_title);
        $this->updateSetting('hero_description', $request->hero_description);
        $this->updateSetting('features', json_decode($request->features, true));

        return response()->json([
            'message' => 'Settings updated successfully',
            'settings' => Setting::pluck('value', 'key')->toArray()
        ]);
    }

    private function updateSetting($key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
} 