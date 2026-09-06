@props([
    'date' => null,
    'format' => 'date',
])

@php
    $formatter = app(\App\Services\JalaliDateFormatter::class);
    $text = match ($format) {
        'full' => $formatter->full($date),
        'day' => $formatter->dayName($date),
        'time' => $formatter->time($date),
        default => $formatter->date($date),
    };
@endphp

<span {{ $attributes }}>{{ $text }}</span>
