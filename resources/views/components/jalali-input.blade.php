@props([
    'label' => null,
    'name',
    'value' => null,
])

<div class="space-y-1.5">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">{{ $label }}</label>
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
        {{ $attributes->merge(['class' => 'kb-input']) }}
    >
    @error($name)
        <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
    @enderror
</div>
