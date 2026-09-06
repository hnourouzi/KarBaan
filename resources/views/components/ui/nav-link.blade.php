@props(['href', 'active' => false, 'icon' => null])

@php
    $classes = $active
        ? 'bg-brand-50 text-brand-700 ring-1 ring-brand-600/10'
        : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => "inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors {$classes}"]) }}
>
    @if ($icon)
        <x-ui.icon :name="$icon" class="h-4 w-4 shrink-0" />
    @endif
    {{ $slot }}
</a>
