<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $notification = Notification::create($request->validated());
        
        if ($request->send_now) {
            $notification->send();
        }

        return redirect()->route('admin.notifications.index')
                        ->with('success', 'Đã tạo thông báo');
    }

    public function broadcast(Request $request)
    {
        // Gửi thông báo cho tất cả người dùng
        \Notification::send(
            User::all(),
            new SystemNotification($request->message)
        );

        return back()->with('success', 'Đã gửi thông báo');
    }

    public function send(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,success,error',
            'user_ids' => 'required|array'
        ]);

        foreach ($request->user_ids as $userId) {
            Notification::create([
                'user_id' => $userId,
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type
            ]);
        }

        return back()->with('success', 'Notifications sent successfully');
    }
} 