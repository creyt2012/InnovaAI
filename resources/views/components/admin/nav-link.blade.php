@props(['active' => false, 'icon' => null])

@php
$classes = $active
    ? 'bg-gray-100 text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md'
    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <x-dynamic-component :component="$icon" class="mr-3 flex-shrink-0 h-6 w-6 text-gray-400 group-hover:text-gray-500"/>
    @endif
    {{ $slot }}
</a> 