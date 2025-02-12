<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingPageController extends Controller
{
    public function edit()
    {
        $landingPage = LandingPage::firstOrCreate([
            'id' => 1
        ], [
            'hero_title' => 'Welcome to ' . config('app.name'),
            'hero_description' => 'AI Chat Assistant powered by LM Studio',
            'contact_email' => 'contact@example.com',
            'meta_title' => config('app.name'),
            'meta_description' => 'AI Chat Assistant'
        ]);

        return view('admin.landing-page.edit', compact('landingPage'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => 'required|string|max:255',
            'hero_description' => 'required|string',
            'hero_image' => 'nullable|image|max:2048',
            'features' => 'nullable|array',
            'features.*.title' => 'required|string',
            'features.*.description' => 'required|string',
            'features.*.icon' => 'required|string',
            'testimonials' => 'nullable|array',
            'testimonials.*.name' => 'required|string',
            'testimonials.*.content' => 'required|string',
            'testimonials.*.avatar' => 'nullable|string',
            'pricing_title' => 'nullable|string',
            'pricing_description' => 'nullable|string',
            'contact_email' => 'required|email',
            'social_links' => 'nullable|array',
            'footer_text' => 'nullable|string',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
            'custom_css' => 'nullable|string',
            'custom_js' => 'nullable|string'
        ]);

        if ($request->hasFile('hero_image')) {
            $path = $request->file('hero_image')->store('public/landing');
            $validated['hero_image'] = Storage::url($path);
        }

        $landingPage = LandingPage::updateOrCreate(
            ['id' => 1],
            $validated
        );

        return back()->with('success', 'Landing page đã được cập nhật');
    }
} 