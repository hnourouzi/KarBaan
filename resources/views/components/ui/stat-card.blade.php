@props([
    'label',
    'value',
    'icon' => null,
    'suffix' => null,
])

<div {{ $attributes->merge(['class' => 'kb-card']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                {{ $value }}
                @if ($suffix)
                    <span class="text-base font-semibold text-slate-500">{{ $suffix }}</span>
                @endif
            </p>
        </div>
        @if ($icon)
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <x-ui.icon :name="$icon" class="h-5 w-5" />
            </div>
        @endif
    </div>
</div>
