<x-app-layout>
    <div class="flex h-screen overflow-hidden">
        <!-- Chat List Sidebar -->
        <div class="w-64 border-r bg-white">
            <div class="h-full flex flex-col">
                <!-- New Chat Button -->
                <div class="p-4">
                    <button @click="newChat" 
                            class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Chat
                    </button>
                </div>

                <!-- Chat List -->
                <div class="flex-1 overflow-y-auto">
                    <nav class="px-2 space-y-1">
                        @foreach($conversations as $conversation)
                            <a href="{{ route('chat.show', $conversation) }}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->route('conversation') == $conversation ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <span class="truncate">{{ $conversation->title }}</span>
                            </a>
                        @endforeach
                    </nav>
                </div>

                <!-- Model Selection -->
                <div class="p-4 border-t bg-gray-50">
                    <label class="block text-sm font-medium text-gray-700">Model</label>
                    <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        @foreach($models as $model)
                            <option value="{{ $model->id }}">{{ $model->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-white">
            <!-- Chat Header -->
            <div class="border-b">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h1 class="text-lg font-medium leading-6 text-gray-900 sm:truncate">
                                {{ $conversation->title ?? 'New Chat' }}
                            </h1>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button class="p-1 text-gray-400 rounded-full hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="sr-only">Voice input</span>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                </svg>
                            </button>
                            <button class="p-1 text-gray-400 rounded-full hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="sr-only">Attach file</span>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                @foreach($messages as $message)
                    <div class="flex items-start {{ $message->isUser() ? 'justify-end' : 'justify-start' }}">
                        <div class="flex-shrink-0">
                            @if($message->isUser())
                                <img class="h-8 w-8 rounded-full" src="{{ Auth::user()->profile_photo_url }}" alt="">
                            @else
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ml-3 max-w-2xl">
                            <div class="rounded-lg px-4 py-2 {{ $message->isUser() ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-900' }}">
                                <div class="prose max-w-none">
                                    {!! $message->content !!}
                                </div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500">
                                {{ $message->created_at->format('g:i A') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Input Area -->
            <div class="border-t p-4">
                <form wire:submit.prevent="sendMessage" class="flex space-x-4">
                    <div class="flex-1">
                        <textarea wire:model="messageInput"
                                 rows="1"
                                 class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                 placeholder="Type your message..."></textarea>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Send
                    </button>
                </form>
            </div>
        </div>

        <!-- Plugin Sidebar -->
        <div class="w-64 border-l bg-white">
            <div class="h-full flex flex-col">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-medium text-gray-900">Plugins</h2>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <!-- Active Plugins -->
                    @foreach($activePlugins as $plugin)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-700">{{ $plugin->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $plugin->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 