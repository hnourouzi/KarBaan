@props(['status'])

@php
    $classes = match ($status) {
        \App\Enums\DayHistoryStatus::Closed => 'kb-badge kb-badge-success',
        \App\Enums\DayHistoryStatus::Open => 'kb-badge kb-badge-warning',
        default => 'kb-badge kb-badge-muted',
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $status->label() }}
</span>
