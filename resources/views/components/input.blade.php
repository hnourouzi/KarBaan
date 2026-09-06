@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'kb-input']) }}
    >
    @error($name)
        <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</div>
