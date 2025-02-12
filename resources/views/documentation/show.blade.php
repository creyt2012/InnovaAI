<x-app-layout>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white border-r">
            <div class="p-4">
                <div class="mb-4">
                    <input type="text" 
                           placeholder="Search docs..."
                           class="w-full px-4 py-2 border rounded"
                           id="docsSearch">
                </div>

                <nav class="space-y-1">
                    @foreach($navigation as $item)
                        <div>
                            @if(isset($item['children']))
                                <div class="font-medium px-4 py-2">{{ $item['title'] }}</div>
                                <div class="ml-4 space-y-1">
                                    @foreach($item['children'] as $child)
                                        <a href="{{ route('docs.show', [$currentSection, $child['slug']]) }}"
                                           class="block px-4 py-2 text-sm {{ $currentPage === $child['slug'] ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                            {{ $child['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <a href="{{ route('docs.show', [$currentSection, $item['slug']]) }}"
                                   class="block px-4 py-2 {{ $currentPage === $item['slug'] ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    {{ $item['title'] }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 bg-gray-50">
            <div class="max-w-4xl mx-auto py-12 px-8">
                <div class="prose max-w-none">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('docsSearch');
        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const query = e.target.value;
                if (query.length >= 3) {
                    fetch(`/docs/search?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(results => {
                            // Update search results UI
                        });
                }
            }, 300);
        });
    </script>
</x-app-layout> 