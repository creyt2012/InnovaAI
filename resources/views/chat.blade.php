<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat với AI</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.8.0/styles/github-dark.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/highlight.js@11.8.0/lib/highlight.min.js"></script>
    <style>
        .chat-container {
            height: calc(100vh - 180px);
        }
        .message-bubble {
            max-width: 85%;
            border-radius: 1rem;
            padding: 1rem;
            margin: 0.5rem;
            position: relative;
            line-height: 1.5;
        }
        .user-message {
            background: #2563eb;
            color: white;
            margin-left: auto;
            border-top-right-radius: 0.2rem;
        }
        .ai-message {
            background: #f3f4f6;
            color: #1f2937;
            margin-right: auto;
            border-top-left-radius: 0.2rem;
        }
        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.25rem;
        }
        pre code {
            border-radius: 0.5rem;
            margin: 1rem 0;
        }
        .copy-button {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.25rem 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .typing-indicator {
            display: flex;
            gap: 0.5rem;
            padding: 1rem;
            background: #f3f4f6;
            border-radius: 1rem;
            margin: 0.5rem;
            width: fit-content;
        }
        .typing-dot {
            width: 0.5rem;
            height: 0.5rem;
            background: #6b7280;
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out;
        }
        .typing-dot:nth-child(1) { animation-delay: 200ms; }
        .typing-dot:nth-child(2) { animation-delay: 300ms; }
        .typing-dot:nth-child(3) { animation-delay: 400ms; }
        @keyframes typing {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm fixed top-0 w-full z-10">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <img src="/logo.png" alt="Logo" class="h-8 w-8">
                    <h1 class="text-xl font-bold text-gray-900">AI Assistant</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button id="theme-toggle" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-moon"></i>
                    </button>
                    <button id="clear-chat" class="text-gray-600 hover:text-gray-900" title="Xóa cuộc trò chuyện">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button id="export-chat" class="text-gray-600 hover:text-gray-900" title="Xuất cuộc trò chuyện">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Chat Container -->
    <main class="max-w-7xl mx-auto px-4 pt-20 pb-6">
        <div class="bg-white rounded-lg shadow-lg flex flex-col chat-container">
            <!-- Chat Messages -->
            <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-4">
                <!-- Messages will be displayed here -->
            </div>

            <!-- Typing Indicator -->
            <div id="typing-indicator" class="typing-indicator hidden">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>

            <!-- Input Area -->
            <div class="border-t p-4 bg-gray-50">
                <form id="chat-form" class="flex items-end space-x-4">
                    <div class="flex-1 relative">
                        <textarea 
                            id="message-input"
                            rows="3"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none pr-12"
                            placeholder="Nhập tin nhắn của bạn..."
                        ></textarea>
                        <button type="button" id="voice-input" class="absolute right-3 bottom-3 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-microphone"></i>
                        </button>
                    </div>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Gửi
                    </button>
                </form>
            </div>
        </div>
    </main>

    <!-- Loading Indicator -->
    <div id="loading-indicator" class="hidden fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">Đang xử lý...</span>
        </div>
    </div>

    <script>
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const chatMessages = document.getElementById('chat-messages');
        const loadingIndicator = document.getElementById('loading-indicator');
        const clearChatBtn = document.getElementById('clear-chat');
        const exportChatBtn = document.getElementById('export-chat');

        // Khởi tạo highlight.js
        hljs.highlightAll();

        // Cấu hình Marked
        marked.setOptions({
            highlight: function(code, lang) {
                if (lang && hljs.getLanguage(lang)) {
                    return hljs.highlight(code, { language: lang }).value;
                }
                return hljs.highlightAuto(code).value;
            },
            breaks: true
        });

        function appendMessage(type, content) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `flex ${type === 'user' ? 'justify-end' : 'justify-start'}`;
            
            const bubbleDiv = document.createElement('div');
            bubbleDiv.className = `max-w-[80%] rounded-lg p-4 ${
                type === 'user' 
                    ? 'bg-blue-600 text-white' 
                    : 'bg-gray-100 text-gray-900'
            }`;

            // Parse markdown và render HTML
            const parsedContent = marked.parse(content);
            bubbleDiv.innerHTML = parsedContent;

            // Highlight code blocks
            bubbleDiv.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });

            messageDiv.appendChild(bubbleDiv);
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;

            // Hiển thị tin nhắn người dùng
            appendMessage('user', message);
            messageInput.value = '';
            messageInput.style.height = 'auto';

            // Hiển thị loading
            loadingIndicator.classList.remove('hidden');

            try {
                const response = await fetch('/api/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();
                appendMessage('ai', data.response);
            } catch (error) {
                console.error('Error:', error);
                appendMessage('ai', '❌ Có lỗi xảy ra. Vui lòng thử lại.');
            } finally {
                loadingIndicator.classList.add('hidden');
            }
        });

        // Auto-resize textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Load chat history
        async function loadHistory() {
            try {
                const response = await fetch('/api/chat/history');
                const history = await response.json();
                
                history.reverse().forEach(chat => {
                    appendMessage('user', chat.user_message);
                    appendMessage('ai', chat.ai_response);
                });
            } catch (error) {
                console.error('Error loading history:', error);
            }
        }

        // Clear chat
        clearChatBtn.addEventListener('click', () => {
            if (confirm('Bạn có chắc muốn xóa toàn bộ tin nhắn?')) {
                chatMessages.innerHTML = '';
                // TODO: Add API call to clear history
            }
        });

        // Export chat
        exportChatBtn.addEventListener('click', () => {
            const messages = [];
            chatMessages.querySelectorAll('.max-w-[80%]').forEach(msg => {
                messages.push(msg.textContent);
            });
            
            const blob = new Blob([messages.join('\n\n')], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'chat-history.txt';
            a.click();
        });

        loadHistory();

        // Thêm các tính năng mới
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Đã sao chép vào clipboard!');
            });
        }

        // Xử lý voice input
        const voiceInputBtn = document.getElementById('voice-input');
        if ('webkitSpeechRecognition' in window) {
            const recognition = new webkitSpeechRecognition();
            recognition.lang = 'vi-VN';
            recognition.continuous = false;
            recognition.interimResults = false;

            recognition.onresult = (event) => {
                const text = event.results[0][0].transcript;
                messageInput.value = text;
            };

            voiceInputBtn.addEventListener('click', () => {
                recognition.start();
                voiceInputBtn.classList.add('text-blue-600');
            });

            recognition.onend = () => {
                voiceInputBtn.classList.remove('text-blue-600');
            };
        } else {
            voiceInputBtn.style.display = 'none';
        }

        // Dark mode toggle
        const themeToggle = document.getElementById('theme-toggle');
        let isDarkMode = false;

        themeToggle.addEventListener('click', () => {
            isDarkMode = !isDarkMode;
            document.body.classList.toggle('dark');
            themeToggle.innerHTML = isDarkMode ? 
                '<i class="fas fa-sun"></i>' : 
                '<i class="fas fa-moon"></i>';
        });
    </script>
</body>
</html> 