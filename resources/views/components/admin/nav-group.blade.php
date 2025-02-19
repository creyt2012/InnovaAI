@props(['label', 'icon' => null])

<div x-data="{ open: false }" class="space-y-1">
    <!-- Group Trigger -->
    <button
        type="button"
        class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900"
        @click="open = !open"
    >
        @if($icon)
            <x-dynamic-component :component="$icon" class="mr-3 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-gray-500"/>
        @endif
        <span class="flex-1 text-left">{{ $label }}</span>
        <x-heroicon-s-chevron-down 
            class="ml-3 h-5 w-5 transform transition-transform duration-150"
            :class="{'rotate-180': open}"
        />
    </button>

    <!-- Group Content -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="pl-4"
    >
        {{ $slot }}
    </div>
</div> 