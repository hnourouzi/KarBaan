@props(['status'])

@php
    $classes = match ($status->value) {
        'done' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'not_done' => 'border-red-200 bg-red-50 text-red-800',
        'extra' => 'border-slate-200 bg-slate-50 text-slate-700',
        default => 'border-stone-200 bg-stone-50 text-stone-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border px-2 py-0.5 text-xs {$classes}"]) }}>
    {{ $status->label() }}
</span>
