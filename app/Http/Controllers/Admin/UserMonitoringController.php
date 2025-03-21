<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ChatSession;
use App\Events\UserActivityBroadcast;
use Illuminate\Http\Request;
use Pusher\Pusher;

class UserMonitoringController extends Controller
{
    public function index()
    {
        $activeUsers = User::whereNotNull('last_activity_at')
            ->where('last_activity_at', '>=', now()->subMinutes(5))
            ->with(['currentSession', 'currentChat'])
            ->get();

        return view('admin.monitoring.index', compact('activeUsers'));
    }

    public function watchUser($userId)
    {
        $user = User::with(['currentSession', 'currentChat'])->findOrFail($userId);
        return view('admin.monitoring.watch', compact('user'));
    }

    public function getLiveChat($userId)
    {
        $chat = ChatSession::where('user_id', $userId)
            ->where('status', 'active')
            ->with('messages')
            ->first();

        return response()->json($chat);
    }

    public function getScreenshot($userId)
    {
        $user = User::findOrFail($userId);
        // Lấy screenshot từ user session
        return response()->json([
            'screenshot' => $user->currentSession->screenshot,
            'timestamp' => now()
        ]);
    }
} 