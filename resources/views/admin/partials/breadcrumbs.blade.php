<nav class="flex" aria-label="Breadcrumb">
    <ol role="list" class="flex items-center space-x-4">
        <li>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-gray-500">
                    <x-heroicon-s-home class="flex-shrink-0 h-5 w-5"/>
                    <span class="sr-only">Trang chủ</span>
                </a>
            </div>
        </li>

        @foreach($breadcrumbs ?? [] as $breadcrumb)
            <li>
                <div class="flex items-center">
                    <x-heroicon-s-chevron-right class="flex-shrink-0 h-5 w-5 text-gray-400"/>
                    <a
                        href="{{ $breadcrumb['url'] }}"
                        class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700"
                        @if($loop->last) aria-current="page" @endif
                    >
                        {{ $breadcrumb['label'] }}
                    </a>
                </div>
            </li>
        @endforeach
    </ol>
</nav> 