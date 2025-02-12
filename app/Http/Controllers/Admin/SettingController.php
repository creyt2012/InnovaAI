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
        return view('admin.settings.index', compact('settings'));
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

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('public/logo');
            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => Storage::url($path)]
            );
        }

        return back()->with('success', 'Logo đã được cập nhật');
    }

    public function updateAIModel(Request $request)
    {
        $request->validate([
            'default_model' => 'required|string',
            'temperature' => 'required|numeric|between:0,1',
            'max_tokens' => 'required|integer|min:1',
            'system_prompt' => 'nullable|string|max:1000'
        ]);

        Setting::updateOrCreate(
            ['key' => 'default_ai_model'],
            ['value' => $request->default_model]
        );

        Setting::updateOrCreate(
            ['key' => 'ai_temperature'],
            ['value' => $request->temperature]
        );

        Setting::updateOrCreate(
            ['key' => 'ai_max_tokens'],
            ['value' => $request->max_tokens]
        );

        Setting::updateOrCreate(
            ['key' => 'system_prompt'],
            ['value' => $request->system_prompt]
        );

        return back()->with('success', 'Cấu hình AI đã được cập nhật');
    }

    private function updateSetting($key, $value)
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
} 