<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Cài đặt hệ thống</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Logo Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Logo</h3>
                    <div class="flex items-center space-x-4">
                        <img src="{{ $settings['site_logo'] ?? '/default-logo.png' }}" 
                             alt="Site Logo" 
                             class="h-16 w-auto">
                        
                        <form action="{{ route('admin.settings.logo') }}" 
                              method="POST" 
                              enctype="multipart/form-data"
                              class="flex items-center space-x-2">
                            @csrf
                            <input type="file" 
                                   name="logo" 
                                   accept="image/*" 
                                   class="border p-2 rounded">
                            <button type="submit" 
                                    class="bg-blue-500 text-white px-4 py-2 rounded">
                                Cập nhật Logo
                            </button>
                        </form>
                    </div>
                </div>

                <!-- AI Model Settings -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Cấu hình AI</h3>
                    <form action="{{ route('admin.settings.ai-model') }}" 
                          method="POST" 
                          class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block mb-2">Model mặc định</label>
                            <select name="default_model" 
                                    class="w-full border rounded p-2">
                                <option value="gpt-3.5-turbo" 
                                        {{ ($settings['default_ai_model'] ?? '') == 'gpt-3.5-turbo' ? 'selected' : '' }}>
                                    GPT-3.5 Turbo
                                </option>
                                <option value="gpt-4" 
                                        {{ ($settings['default_ai_model'] ?? '') == 'gpt-4' ? 'selected' : '' }}>
                                    GPT-4
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2">Temperature</label>
                            <input type="number" 
                                   name="temperature" 
                                   step="0.1" 
                                   min="0" 
                                   max="1"
                                   value="{{ $settings['ai_temperature'] ?? 0.7 }}"
                                   class="w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block mb-2">Max Tokens</label>
                            <input type="number" 
                                   name="max_tokens"
                                   min="1"
                                   value="{{ $settings['ai_max_tokens'] ?? 2000 }}"
                                   class="w-full border rounded p-2">
                        </div>

                        <div>
                            <label class="block mb-2">System Prompt</label>
                            <textarea name="system_prompt"
                                      rows="4"
                                      class="w-full border rounded p-2">{{ $settings['system_prompt'] ?? '' }}</textarea>
                        </div>

                        <button type="submit" 
                                class="bg-blue-500 text-white px-4 py-2 rounded">
                            Lưu cấu hình
                        </button>
                    </form>
                </div>

                <!-- User Management -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Quản lý người dùng</h3>
                    <a href="{{ route('admin.users.index') }}" 
                       class="bg-green-500 text-white px-4 py-2 rounded">
                        Quản lý người dùng
                    </a>
                </div>

                <!-- Node Management -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium mb-4">Quản lý Node LM Studio</h3>
                    <a href="{{ route('admin.lmstudio.nodes.index') }}" 
                       class="bg-purple-500 text-white px-4 py-2 rounded">
                        Quản lý Node
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 