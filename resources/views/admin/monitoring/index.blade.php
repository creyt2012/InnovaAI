@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">User Monitoring</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($activeUsers as $user)
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">{{ $user->name }}</h3>
                <span class="text-sm text-green-500">Online</span>
            </div>

            <div class="space-y-2">
                <div class="aspect-video bg-gray-100 rounded relative">
                    <img 
                        src="" 
                        class="user-screen"
                        data-user-id="{{ $user->id }}"
                        alt="User Screen"
                    >
                    <div class="absolute bottom-2 right-2">
                        <button 
                            class="watch-live-btn bg-blue-500 text-white px-3 py-1 rounded"
                            data-user-id="{{ $user->id }}"
                        >
                            Watch Live
                        </button>
                    </div>
                </div>

                <div class="text-sm text-gray-600">
                    <p>Current URL: <span class="current-url-{{ $user->id }}">{{ $user->currentSession->current_url ?? 'N/A' }}</span></p>
                    <p>Last Activity: <span class="last-activity-{{ $user->id }}">{{ $user->last_activity_at->diffForHumans() }}</span></p>
                </div>

                @if($user->currentChat)
                <div class="mt-4">
                    <h4 class="font-medium mb-2">Current Chat</h4>
                    <div class="chat-messages-{{ $user->id }} h-32 overflow-y-auto bg-gray-50 p-2 rounded">
                        @foreach($user->currentChat->messages as $message)
                        <div class="mb-2">
                            <span class="font-medium">{{ $message->role }}:</span>
                            <span>{{ $message->content }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo Pusher
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}'
    });

    // Theo dõi mỗi user
    @foreach($activeUsers as $user)
    const channel{{ $user->id }} = pusher.subscribe('user-monitoring.{{ $user->id }}');
    
    channel{{ $user->id }}.bind('UserActivityBroadcast', function(data) {
        // Cập nhật screenshot
        document.querySelector('.user-screen[data-user-id="{{ $user->id }}"]').src = data.screenshot;
        
        // Cập nhật thông tin khác
        document.querySelector('.current-url-{{ $user->id }}').textContent = data.activity.current_url;
        document.querySelector('.last-activity-{{ $user->id }}').textContent = data.timestamp;
        
        // Cập nhật chat nếu có
        if (data.activity.chat_message) {
            const chatContainer = document.querySelector('.chat-messages-{{ $user->id }}');
            const messageDiv = document.createElement('div');
            messageDiv.className = 'mb-2';
            messageDiv.innerHTML = `
                <span class="font-medium">${data.activity.chat_message.role}:</span>
                <span>${data.activity.chat_message.content}</span>
            `;
            chatContainer.appendChild(messageDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
    @endforeach

    // Xử lý nút Watch Live
    document.querySelectorAll('.watch-live-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            window.open(`/admin/monitoring/watch/${userId}`, '_blank');
        });
    });
});
</script>
@endpush
@endsection 