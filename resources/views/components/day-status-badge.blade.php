@props(['status'])

@php
    $classes = match ($status) {
        \App\Enums\DayHistoryStatus::Closed => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        \App\Enums\DayHistoryStatus::Open => 'border-amber-200 bg-amber-50 text-amber-800',
        default => 'border-stone-200 bg-stone-50 text-stone-600',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border px-2 py-0.5 text-xs {$classes}"]) }}>
    {{ $status->label() }}
</span>
