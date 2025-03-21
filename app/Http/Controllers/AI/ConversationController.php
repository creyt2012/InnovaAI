<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = Auth::user()
            ->conversations()
            ->with(['lastMessage'])
            ->latest()
            ->paginate(10);

        return response()->json($conversations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'system_prompt' => 'nullable|string|max:1000'
        ]);

        $conversation = Conversation::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'system_prompt' => $request->system_prompt ?? config('ai.default_system_prompt')
        ]);

        return response()->json($conversation, 201);
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load(['messages' => function ($query) {
            $query->latest()->paginate(50);
        }]);

        return response()->json($conversation);
    }

    public function update(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'title' => 'required|string|max:255',
            'system_prompt' => 'nullable|string|max:1000'
        ]);

        $conversation->update($request->only(['title', 'system_prompt']));

        return response()->json($conversation);
    }

    public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);
        
        $conversation->delete();

        return response()->noContent();
    }
} 