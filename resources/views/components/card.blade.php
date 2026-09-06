@props(['title' => null])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-stone-200 bg-white p-5']) }}>
    @if ($title)
        <h2 class="mb-4 text-base font-semibold text-stone-900">{{ $title }}</h2>
    @endif
    {{ $slot }}
</section>
