@props(['status'])

@php
    $classes = match ($status) {
        \App\Enums\AttendanceStatus::Finished => 'kb-badge kb-badge-success',
        \App\Enums\AttendanceStatus::Started => 'kb-badge kb-badge-warning',
        default => 'kb-badge kb-badge-muted',
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $status->label() }}
</span>
