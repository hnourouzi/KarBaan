@props([
    'label' => null,
    'name',
    'value' => null,
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm text-stone-700">{{ $label }}</label>
    @endif
    <input
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        data-jdp
        autocomplete="off"
        inputmode="numeric"
        placeholder="۱۴۰۴/۰۶/۱۵"
        {{ $attributes->merge(['class' => 'w-full rounded-md border border-stone-300 bg-white px-3 py-2 text-sm text-stone-800 outline-none focus:border-stone-500']) }}
    >
    @error($name)
        <p class="text-xs text-red-700">{{ $message }}</p>
    @enderror
</div>
