@props([
    'label' => null,
    'name',
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm text-stone-700">{{ $label }}</label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge(['class' => 'w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm text-stone-800 outline-none focus:border-stone-500']) }}
    >
        {{ $slot }}
    </select>
    @error($name)
        <p class="text-xs text-red-700">{{ $message }}</p>
    @enderror
</div>
