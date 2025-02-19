<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Cấu hình hệ thống</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('admin.config.update') }}" method="POST">
                    @csrf
                    @method('POST')

                    @foreach($configs as $group => $groupConfigs)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">{{ $group }}</h3>
                            
                            @foreach($groupConfigs as $config)
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ $config->key }}
                                    </label>
                                    
                                    @if($config->type === 'boolean')
                                        <select name="configs[{{ $config->key }}]" class="mt-1 block w-full rounded-md border-gray-300">
                                            <option value="1" {{ $config->value ? 'selected' : '' }}>Bật</option>
                                            <option value="0" {{ !$config->value ? 'selected' : '' }}>Tắt</option>
                                        </select>
                                    @else
                                        <input type="text" 
                                               name="configs[{{ $config->key }}]"
                                               value="{{ $config->value }}"
                                               class="mt-1 block w-full rounded-md border-gray-300">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <div class="mt-6">
                        <x-button type="submit">
                            Lưu cấu hình
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 