@props(['status'])

@php
    $classes = match ($status->value) {
        'done' => 'kb-badge kb-badge-success',
        'not_done' => 'kb-badge kb-badge-danger',
        'extra' => 'kb-badge kb-badge-info',
        default => 'kb-badge kb-badge-muted',
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $status->label() }}
</span>
