<x-app-layout>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white border-r">
            <div class="p-4">
                <h2 class="text-lg font-semibold mb-4">Documentation</h2>
                <div class="space-y-2">
                    @foreach($sections as $key => $title)
                        <a href="{{ route('docs.show', $key) }}"
                           class="block px-4 py-2 rounded hover:bg-gray-100">
                            {{ $title }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 bg-gray-50">
            <div class="max-w-4xl mx-auto py-12 px-8">
                <h1 class="text-3xl font-bold mb-8">Welcome to LM Studio Documentation</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($sections as $key => $title)
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-xl font-semibold mb-2">{{ $title }}</h3>
                            <p class="text-gray-600 mb-4">
                                Learn about {{ strtolower($title) }} and features.
                            </p>
                            <a href="{{ route('docs.show', $key) }}"
                               class="text-blue-600 hover:text-blue-800">
                                Read More →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 