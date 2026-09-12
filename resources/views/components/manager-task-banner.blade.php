@props(['plan'])

@php
    $pending = $plan->tasks->filter(
        fn ($task) => $task->isAssignedByManager() && $task->isPlanned()
    )->count();
@endphp

@if ($pending > 0)
    <div {{ $attributes->merge(['class' => 'mb-6 flex items-start gap-3 rounded-xl border border-brand-100 bg-brand-50 px-4 py-3 text-sm text-brand-700 shadow-sm']) }} role="status">
        <x-ui.icon name="clipboard" class="mt-0.5 h-5 w-5 shrink-0" />
        <p>
            {{ $pending }} وظیفه جدید توسط مدیر اضافه شده
        </p>
    </div>
@endif
