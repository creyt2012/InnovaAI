<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
        <div class="flex">
            <!-- Mobile menu button -->
            <button type="button" class="px-4 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">
                <span class="sr-only">Mở menu</span>
                <x-heroicon-o-menu class="h-6 w-6"/>
            </button>
        </div>

        <div class="flex items-center">
            <!-- Search -->
            <div class="flex-1 px-2 flex justify-center lg:ml-6 lg:justify-end">
                <div class="max-w-lg w-full lg:max-w-xs">
                    <label for="search" class="sr-only">Tìm kiếm</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-s-search class="h-5 w-5 text-gray-400"/>
                        </div>
                        <input
                            id="search"
                            name="search"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Tìm kiếm"
                            type="search"
                        >
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <button type="button" class="flex-shrink-0 p-1 ml-4 text-gray-400 rounded-full hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <span class="sr-only">Xem thông báo</span>
                <x-heroicon-o-bell class="h-6 w-6"/>
            </button>

            <!-- Profile dropdown -->
            <div class="ml-3 relative" x-data="{ open: false }">
                <div>
                    <button
                        type="button"
                        class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        @click="open = !open"
                    >
                        <span class="sr-only">Mở menu người dùng</span>
                        <img class="h-8 w-8 rounded-full" src="{{ auth()->user()->profile_photo_url }}" alt="">
                    </button>
                </div>

                <div
                    x-show="open"
                    @click.away="open = false"
                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                    <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Hồ sơ
                    </a>
                    <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Cài đặt
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 