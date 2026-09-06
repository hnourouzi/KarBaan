@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'mb-8 flex flex-wrap items-end justify-between gap-4']) }}>
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1.5 text-sm text-slate-500">{{ $description }}</p>
        @endif
        @if ($slot->isNotEmpty())
            <div class="mt-1.5 text-sm text-slate-500">{{ $slot }}</div>
        @endif
    </div>
    @if (isset($actions) && ! $actions->isEmpty())
        <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
    @endif
</div>
