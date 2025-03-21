<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Events\UserActivityBroadcast;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check()) {
            $user = auth()->user();
            $user->update(['last_activity_at' => now()]);

            // Broadcast activity
            broadcast(new UserActivityBroadcast(
                $user->id,
                [
                    'current_url' => $request->fullUrl(),
                    'chat_message' => session('last_chat_message')
                ]
            ));
        }

        return $response;
    }
} 