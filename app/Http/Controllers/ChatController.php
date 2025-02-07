<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\QueryProcessingService;
use App\Services\LMStudioService;
use App\Models\Chat;

class ChatController extends Controller
{
    protected $queryService;
    protected $lmStudioService;

    public function __construct(QueryProcessingService $queryService, LMStudioService $lmStudioService)
    {
        $this->queryService = $queryService;
        $this->lmStudioService = $lmStudioService;
    }

    public function index()
    {
        $conversations = auth()->user()->conversations()
            ->with(['messages' => function ($query) {
                $query->latest()->first();
            }])
            ->latest()
            ->get();

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations
        ]);
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->load('messages');

        return Inertia::render('Chat/Show', [
            'conversation' => $conversation
        ]);
    }

    public function store(Request $request)
    {
        $conversation = Conversation::create([
            'user_id' => auth()->id(),
            'title' => 'New Chat'
        ]);

        if ($request->message) {
            $message = $conversation->messages()->create([
                'role' => 'user',
                'content' => $request->message
            ]);

            // Process the message
            $response = $this->queryService->processQuery($request->message, auth()->user());

            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $response
            ]);

            // Update conversation title based on first message
            $conversation->update([
                'title' => substr($request->message, 0, 50) . '...'
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function message(Request $request, Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:txt,pdf,doc,docx'
        ]);

        try {
            // Lưu tin nhắn của user
            $userMessage = $conversation->messages()->create([
                'role' => 'user',
                'content' => $request->message
            ]);

            // Xử lý file đính kèm
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    $attachment = $userMessage->attachments()->create([
                        'filename' => basename($path),
                        'original_filename' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'path' => $path
                    ]);
                    $attachments[] = $attachment;
                }
            }

            // Xử lý query với LM Studio
            $response = $this->queryService->processQuery(
                $request->message,
                auth()->user(),
                $attachments
            );

            // Lưu response từ assistant
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $response
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'response' => $response
                ]);
            }

            return back();

        } catch (\Exception $e) {
            \Log::error('Chat processing error: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Có lỗi xảy ra khi xử lý yêu cầu'
                ], 500);
            }

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi xử lý yêu cầu']);
        }
    }

    public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);
        
        $conversation->delete();
        
        return redirect()->route('chat.index');
    }

    public function chat(Request $request)
    {
        $message = $request->input('message');
        $response = $this->lmStudioService->sendMessage($message);
        
        // Lưu chat history
        Chat::create([
            'user_message' => $message,
            'ai_response' => $response,
            'user_id' => auth()->id()
        ]);

        return response()->json(['response' => $response]);
    }

    public function history()
    {
        $history = Chat::where('user_id', auth()->id())
                      ->orderBy('created_at', 'desc')
                      ->get();
        return response()->json($history);
    }
} 