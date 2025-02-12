<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LM Studio') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
</head>
<body class="font-sans antialiased bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
             @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <div x-show="sidebarOpen"
             class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r lg:static lg:block">
            @include('layouts.sidebar')
        </div>

        <!-- Main Content -->
        <div class="flex flex-col min-h-screen lg:pl-64">
            <!-- Top Navigation -->
            <nav class="sticky top-0 z-30 bg-white border-b">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Left side -->
                        <div class="flex">
                            <button @click="sidebarOpen = !sidebarOpen"
                                    class="px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 lg:hidden">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            
                            <!-- Search -->
                            <div class="flex items-center px-4">
                                <div class="w-full max-w-lg lg:max-w-xs">
                                    <label for="search" class="sr-only">Search</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                        <input type="search" 
                                               class="block w-full py-2 pl-10 pr-3 leading-5 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                               placeholder="Search...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right side -->
                        <div class="flex items-center">
                            <div class="flex items-center space-x-4">
                                <!-- Notifications -->
                                <button class="p-1 text-gray-400 rounded-full hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <span class="sr-only">View notifications</span>
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </button>

                                <!-- Profile dropdown -->
                                <div class="relative ml-3" x-data="{ open: false }">
                                    <div>
                                        <button @click="open = !open" 
                                                class="flex items-center max-w-xs text-sm bg-white rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <img class="w-8 h-8 rounded-full" 
                                                 src="{{ Auth::user()->profile_photo_url }}" 
                                                 alt="{{ Auth::user()->name }}">
                                        </button>
                                    </div>
                                    
                                    <div x-show="open"
                                         @click.away="open = false"
                                         class="absolute right-0 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                        <!-- Profile menu items -->
                                        <a href="{{ route('profile.show') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Your Profile
                                        </a>
                                        <a href="{{ route('profile.settings') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Settings
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" 
                                                    class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                                                Sign out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content Area -->
            <main class="flex-1">
                <div class="py-6">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('modals')
    @livewireScripts
    @stack('scripts')
</body>
</html> 