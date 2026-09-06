@props([
    'type' => 'submit',
    'variant' => 'primary',
    'href' => null,
])

@php
    $classes = match ($variant) {
        'secondary' => 'border border-stone-300 bg-white text-stone-800 hover:bg-stone-50',
        'danger' => 'border border-red-800 bg-red-800 text-white hover:bg-red-900',
        default => 'border border-stone-800 bg-stone-800 text-white hover:bg-stone-900',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium {$classes}"]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium {$classes}"]) }}>
        {{ $slot }}
    </button>
@endif
