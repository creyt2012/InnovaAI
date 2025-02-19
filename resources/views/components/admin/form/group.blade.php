@props(['label', 'for' => null, 'error' => null, 'helpText' => null])

<div {{ $attributes }}>
    @if($label)
        <label for="{{ $for }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    {{ $slot }}

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif

    @if($helpText)
        <p class="mt-1 text-sm text-gray-500">{{ $helpText }}</p>
    @endif
</div> 