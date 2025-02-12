<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $landingPage->meta_title }}</title>
    <meta name="description" content="{{ $landingPage->meta_description }}">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        {!! $landingPage->custom_css !!}
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
                    {{ $landingPage->hero_title }}
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    {{ $landingPage->hero_description }}
                </p>
                @if($landingPage->hero_image)
                    <div class="mt-8">
                        <img src="{{ $landingPage->hero_image }}" 
                             alt="Hero" 
                             class="mx-auto">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Features Section -->
    @if($landingPage->features)
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($landingPage->features as $feature)
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="text-3xl mb-4">
                        {!! $feature['icon'] !!}
                    </div>
                    <h3 class="text-xl font-medium mb-2">
                        {{ $feature['title'] }}
                    </h3>
                    <p class="text-gray-600">
                        {{ $feature['description'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <script>
        {!! $landingPage->custom_js !!}
    </script>
</body>
</html> 