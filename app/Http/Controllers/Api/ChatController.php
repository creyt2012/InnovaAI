<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QueryProcessingService;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected $queryProcessor;

    public function __construct(QueryProcessingService $queryProcessor)
    {
        $this->queryProcessor = $queryProcessor;
    }

    public function index()
    {
        $chats = Chat::with(['messages' => function ($query) {
                $query->latest()->take(1);
            }])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $chats
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $chat = Chat::create([
                'user_id' => Auth::id(),
                'title' => substr($request->message, 0, 50) . '...'
            ]);

            $message = Message::create([
                'chat_id' => $chat->id,
                'content' => $request->message,
                'role' => 'user'
            ]);

            // Xử lý file đính kèm
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    $message->attachments()->create([
                        'path' => $path,
                        'original_filename' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                }
            }

            // Xử lý câu hỏi và tạo phản hồi
            $response = $this->queryProcessor->processQuery(
                $request->message,
                Auth::user(),
                $message->attachments
            );

            Message::create([
                'chat_id' => $chat->id,
                'content' => $response,
                'role' => 'assistant'
            ]);

            return response()->json([
                'success' => true,
                'data' => $chat->load('messages')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $chat = Chat::with('messages.attachments')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $chat
        ]);
    }

    public function message(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $chat = Chat::where('user_id', Auth::id())->findOrFail($id);

            $message = Message::create([
                'chat_id' => $chat->id,
                'content' => $request->message,
                'role' => 'user'
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    $message->attachments()->create([
                        'path' => $path,
                        'original_filename' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                }
            }

            $response = $this->queryProcessor->processQuery(
                $request->message,
                Auth::user(),
                $message->attachments
            );

            Message::create([
                'chat_id' => $chat->id,
                'content' => $response,
                'role' => 'assistant'
            ]);

            return response()->json([
                'success' => true,
                'data' => $chat->load('messages')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing message: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $chat = Chat::where('user_id', Auth::id())->findOrFail($id);
        $chat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat deleted successfully'
        ]);
    }
} 