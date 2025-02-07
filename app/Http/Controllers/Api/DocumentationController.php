<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\QueryParam;

#[Group('Chat API')]
class ChatController extends Controller
{
    /**
     * Get list of chats
     * 
     * Returns paginated list of user's chats with latest message
     */
    #[Response([
        'success' => true,
        'data' => [
            [
                'id' => 1,
                'user_id' => 1,
                'title' => 'Chat title...',
                'created_at' => '2024-03-20T10:00:00Z',
                'messages' => [
                    [
                        'id' => 1,
                        'content' => 'Latest message',
                        'role' => 'user'
                    ]
                ]
            ]
        ]
    ])]
    public function index() { /* ... */ }

    /**
     * Create new chat
     * 
     * Creates a new chat with initial message and optional attachments
     */
    #[BodyParam('message', 'string', 'Chat message content', required: true)]
    #[BodyParam('attachments[]', 'file', 'Optional file attachments (max 10MB each)')]
    #[Response([
        'success' => true,
        'data' => [
            'id' => 1,
            'title' => 'Chat title...',
            'messages' => [
                [
                    'id' => 1,
                    'content' => 'User message',
                    'role' => 'user'
                ],
                [
                    'id' => 2, 
                    'content' => 'AI response',
                    'role' => 'assistant'
                ]
            ]
        ]
    ])]
    public function store() { /* ... */ }
}

#[Group('Server Management API')]
class ServerController extends Controller
{
    /**
     * Get list of servers
     * 
     * Returns list of all servers with their latest metrics
     * Requires admin role
     */
    #[Response([
        'success' => true,
        'data' => [
            [
                'id' => 1,
                'name' => 'Server 1',
                'host' => 'localhost',
                'port' => 8000,
                'status' => 'active',
                'latest_metrics' => [
                    'cpu_usage' => 45.5,
                    'memory_usage' => 60.2
                ]
            ]
        ]
    ])]
    public function index() { /* ... */ }

    /**
     * Add new server
     * 
     * Creates a new server instance
     * Requires admin role
     */
    #[BodyParam('name', 'string', 'Server name', required: true)]
    #[BodyParam('host', 'string', 'Server hostname/IP', required: true)]
    #[BodyParam('port', 'integer', 'Server port (1-65535)', required: true)]
    #[BodyParam('configuration', 'json', 'Server configuration')]
    #[Response([
        'success' => true,
        'data' => [
            'id' => 1,
            'name' => 'New Server',
            'host' => 'localhost',
            'port' => 8000
        ]
    ], 201)]
    public function store() { /* ... */ }
} 