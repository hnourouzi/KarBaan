@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'id' => null,
    'remember' => true,
])

@php
    $inputId = $id ?? $name;
@endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $inputId }}"
        value="{{ $remember ? old($name, $value) : $value }}"
        {{ $attributes->merge(['class' => 'kb-input']) }}
    >
    @if ($remember)
        @error($name)
            <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    @endif
</div>
