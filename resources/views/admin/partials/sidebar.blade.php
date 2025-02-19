<div class="h-full flex flex-col">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 border-b">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-8">
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <div class="px-4 space-y-1">
            <!-- Dashboard -->
            <x-admin.nav-link 
                href="{{ route('admin.dashboard') }}"
                icon="heroicon-o-home"
                :active="request()->routeIs('admin.dashboard')">
                Dashboard
            </x-admin.nav-link>

            <!-- System -->
            <x-admin.nav-group label="Hệ thống" icon="heroicon-o-cog">
                <x-admin.nav-link 
                    href="{{ route('admin.config.index') }}"
                    icon="heroicon-o-adjustments"
                    :active="request()->routeIs('admin.config.*')">
                    Cấu hình
                </x-admin.nav-link>
                
                <x-admin.nav-link 
                    href="{{ route('admin.security.index') }}"
                    icon="heroicon-o-shield-check"
                    :active="request()->routeIs('admin.security.*')">
                    Bảo mật
                </x-admin.nav-link>
            </x-admin.nav-group>

            <!-- Users -->
            <x-admin.nav-group label="Người dùng" icon="heroicon-o-users">
                <x-admin.nav-link 
                    href="{{ route('admin.users.index') }}"
                    :active="request()->routeIs('admin.users.index')">
                    Danh sách
                </x-admin.nav-link>
                
                <x-admin.nav-link 
                    href="{{ route('admin.roles.index') }}"
                    :active="request()->routeIs('admin.roles.*')">
                    Phân quyền
                </x-admin.nav-link>
            </x-admin.nav-group>

            <!-- Analytics -->
            <x-admin.nav-group label="Phân tích" icon="heroicon-o-chart-bar">
                <x-admin.nav-link href="{{ route('admin.analytics.overview') }}">
                    Tổng quan
                </x-admin.nav-link>
                <x-admin.nav-link href="{{ route('admin.analytics.users') }}">
                    Người dùng
                </x-admin.nav-link>
                <x-admin.nav-link href="{{ route('admin.analytics.chat') }}">
                    Chat
                </x-admin.nav-link>
            </x-admin.nav-group>
        </div>
    </nav>
</div> 