@props([
    'title' => null,
    'description' => null,
    'close' => 'open = false',
    'maxWidth' => 'max-w-xl',
])

<div
    {{ $attributes->merge(['class' => 'fixed inset-0 z-50']) }}
    role="dialog"
    aria-modal="true"
    x-cloak
>
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" x-on:click="{{ $close }}"></div>
    <div class="relative flex h-full items-center justify-center p-4">
        <div class="max-h-[90vh] w-full {{ $maxWidth }} overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-2xl" x-on:click.stop>
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        @isset($heading)
                            {{ $heading }}
                        @else
                            {{ $title }}
                        @endisset
                    </h2>
                    @if ($description)
                        <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
                    @endif
                </div>
                <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-slate-800" x-on:click="{{ $close }}" aria-label="بستن">
                    <x-ui.icon name="x-mark" class="h-5 w-5" />
                </button>
            </div>
            <div class="overflow-y-auto px-6 py-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
