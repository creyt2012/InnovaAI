<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'smtp' => [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'encryption' => config('mail.mailers.smtp.encryption'),
            ],
            'app' => [
                'logo' => setting('app.logo'),
                'favicon' => setting('app.favicon'),
                'name' => config('app.name'),
            ]
        ];

        return view('admin.settings.system', compact('settings'));
    }

    public function updateSmtp(Request $request)
    {
        $validated = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'required|in:tls,ssl',
            'from_address' => 'required|email',
            'from_name' => 'required|string'
        ]);

        // Cập nhật cấu hình SMTP
        setting([
            'mail.mailers.smtp.host' => $validated['host'],
            'mail.mailers.smtp.port' => $validated['port'],
            'mail.mailers.smtp.username' => $validated['username'],
            'mail.mailers.smtp.password' => $validated['password'],
            'mail.mailers.smtp.encryption' => $validated['encryption'],
            'mail.from.address' => $validated['from_address'],
            'mail.from.name' => $validated['from_name']
        ])->save();

        return back()->with('success', 'SMTP settings updated successfully');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024'
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('public/logos');
            setting(['app.logo' => Storage::url($logoPath)])->save();
        }

        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $faviconPath = $favicon->store('public/logos');
            setting(['app.favicon' => Storage::url($faviconPath)])->save();
        }

        return back()->with('success', 'Logo updated successfully');
    }

    public function testSmtp(Request $request)
    {
        try {
            $to = $request->input('test_email', config('mail.from.address'));
            
            \Mail::raw('Test email from ' . config('app.name'), function($message) use ($to) {
                $message->to($to)
                    ->subject('SMTP Test Email');
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SMTP test failed: ' . $e->getMessage()
            ], 400);
        }
    }
} 