@props([
    'label' => null,
    'name',
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'kb-input']) }}
    >
        {{ $slot }}
    </select>
    @error($name)
        <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</div>
