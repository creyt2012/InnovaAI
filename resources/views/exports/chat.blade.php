<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Chat Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .message { margin-bottom: 20px; }
        .user { color: #2563eb; }
        .assistant { color: #1f2937; }
        .timestamp { color: #6b7280; font-size: 0.8em; }
    </style>
</head>
<body>
    <h1>Chat History</h1>
    <p>Exported on: {{ now()->format('Y-m-d H:i:s') }}</p>

    @foreach($chat->messages as $message)
        <div class="message">
            <div class="{{ $message->role }}">
                <strong>{{ ucfirst($message->role) }}:</strong>
                {{ $message->content }}
            </div>
            <div class="timestamp">
                {{ $message->created_at->format('Y-m-d H:i:s') }}
            </div>
        </div>
    @endforeach
</body>
</html> 