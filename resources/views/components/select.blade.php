@props([
    'label' => null,
    'name',
    'id' => null,
    'remember' => true,
])

@php
    $selectId = $id ?? $name;
@endphp

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $selectId }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $selectId }}"
        {{ $attributes->merge(['class' => 'kb-input']) }}
    >
        {{ $slot }}
    </select>
    @if ($remember)
        @error($name)
            <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
        @enderror
    @endif
</div>
