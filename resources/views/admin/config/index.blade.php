@extends('layouts.admin')

@section('title', 'Cấu hình hệ thống')

@section('content')
<div class="space-y-6">
    <!-- General Settings -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Cài đặt chung
            </h2>

            <form action="{{ route('admin.config.update') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Site Name -->
                    <div>
                        <x-admin.form.group label="Tên trang web" for="site_name">
                            <x-admin.form.input 
                                id="site_name"
                                name="configs[site_name]"
                                :value="$configs->get('site_name')"
                                required
                            />
                        </x-admin.form.group>
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <x-admin.form.group label="Email liên hệ" for="contact_email">
                            <x-admin.form.input 
                                type="email"
                                id="contact_email" 
                                name="configs[contact_email]"
                                :value="$configs->get('contact_email')"
                            />
                        </x-admin.form.group>
                    </div>
                </div>

                <div class="mt-6">
                    <x-button type="submit">
                        Lưu thay đổi
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Chat Settings -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                Cài đặt chat
            </h2>

            <form action="{{ route('admin.config.update') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Max Tokens -->
                    <div>
                        <x-admin.form.group label="Số token tối đa" for="chat_max_tokens">
                            <x-admin.form.input 
                                type="number"
                                id="chat_max_tokens"
                                name="configs[chat_max_tokens]"
                                :value="$configs->get('chat_max_tokens')"
                                min="1"
                                max="4096"
                            />
                        </x-admin.form.group>
                    </div>

                    <!-- Temperature -->
                    <div>
                        <x-admin.form.group label="Temperature" for="chat_temperature">
                            <x-admin.form.input 
                                type="number"
                                id="chat_temperature"
                                name="configs[chat_temperature]"
                                :value="$configs->get('chat_temperature')"
                                step="0.1"
                                min="0"
                                max="2"
                            />
                        </x-admin.form.group>
                    </div>
                </div>

                <div class="mt-6">
                    <x-button type="submit">
                        Lưu thay đổi
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    <!-- Maintenance Mode -->
    <div class="bg-white shadow rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900">
                    Chế độ bảo trì
                </h2>

                <div>
                    <x-admin.toggle
                        :checked="$configs->get('maintenance_mode')"
                        wire:click="toggleMaintenance"
                    />
                </div>
            </div>

            <div class="prose prose-sm max-w-none">
                <p class="text-gray-500">
                    Khi bật chế độ bảo trì, chỉ admin mới có thể truy cập trang web.
                    Người dùng thông thường sẽ thấy thông báo bảo trì.
                </p>
            </div>

            @if($configs->get('maintenance_mode'))
                <div class="mt-4 p-4 bg-yellow-50 rounded-md">
                    <div class="flex">
                        <div class="shrink-0">
                            <x-heroicon-s-exclamation class="h-5 w-5 text-yellow-400"/>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">
                                Chế độ bảo trì đang bật
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>
                                    Trang web hiện chỉ có thể truy cập bởi admin.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 