<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use App\Http\Requests\UpdateSystemConfigRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemConfigController extends Controller
{
    public function index()
    {
        $configs = SystemConfig::all()->groupBy('group');
        return view('admin.system.config', compact('configs'));
    }

    public function update(UpdateSystemConfigRequest $request)
    {
        foreach ($request->validated()['configs'] as $key => $value) {
            SystemConfig::set($key, $value);
        }

        Cache::tags('system_config')->flush();
        return back()->with('success', 'Cấu hình đã được cập nhật');
    }

    public function maintenance(Request $request)
    {
        $this->validate($request, [
            'enable' => 'required|boolean',
            'message' => 'nullable|string',
            'retry' => 'nullable|integer'
        ]);

        if ($request->enable) {
            \Artisan::call('down', [
                '--message' => $request->message,
                '--retry' => $request->retry ?? 60
            ]);
        } else {
            \Artisan::call('up');
        }

        return back()->with('success', 'Đã thay đổi trạng thái bảo trì');
    }
} 