<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @livewireStyles
    
    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r transform transition-transform duration-300">
            @include('admin.partials.sidebar')
        </div>

        <!-- Main Content -->
        <div class="pl-64">
            <!-- Top Navigation -->
            <nav class="bg-white border-b h-16">
                @include('admin.partials.topnav')
            </nav>

            <!-- Page Content -->
            <main class="p-6">
                <!-- Breadcrumbs -->
                <div class="mb-6">
                    @include('admin.partials.breadcrumbs')
                </div>

                <!-- Page Heading -->
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        @yield('title')
                    </h1>
                </div>

                <!-- Content -->
                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals')
    @livewireScripts
    @stack('scripts')
</body>
</html> 