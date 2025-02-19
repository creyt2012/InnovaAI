<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'permissions'])
                    ->withCount(['chats', 'logins'])
                    ->paginate(20);
                    
        return view('admin.users.index', compact('users'));
    }

    public function ban(User $user)
    {
        $user->update(['is_banned' => true]);
        return back()->with('success', 'Người dùng đã bị cấm');
    }

    public function impersonate(User $user)
    {
        // Cho phép admin đăng nhập với tư cách người dùng
        session()->put('impersonating', $user->id);
        return redirect()->route('chat.index');
    }
} 