<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Plugin Marketplace</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($plugins as $id => $plugin)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center mb-4">
                                <div class="text-2xl mr-3">
                                    <i class="{{ $plugin['icon'] ?? 'fas fa-puzzle-piece' }}"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium">{{ $plugin['name'] }}</h3>
                                    <p class="text-sm text-gray-500">v{{ $plugin['version'] }}</p>
                                </div>
                            </div>

                            <p class="text-gray-600 mb-4">{{ $plugin['description'] }}</p>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500">
                                    By {{ $plugin['author'] }}
                                </span>
                                
                                @if($plugin['price'] > 0)
                                    <span class="text-green-600 font-medium">
                                        ${{ number_format($plugin['price'], 2) }}
                                    </span>
                                @else
                                    <span class="text-green-600">Free</span>
                                @endif
                            </div>

                            <div class="mt-4">
                                @if($installedPlugins->contains('slug', $id))
                                    <form action="{{ route('plugins.uninstall', $id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full bg-red-500 text-white px-4 py-2 rounded">
                                            Uninstall
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('plugins.install', $id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full bg-blue-500 text-white px-4 py-2 rounded">
                                            Install
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 