@props(['task'])

@php
    $mark = match ($task->status->value) {
        'done' => '✓',
        'not_done' => '✕',
        'extra' => '+',
        default => '○',
    };

    $markClass = match ($task->status->value) {
        'done' => 'border-emerald-700 text-emerald-700',
        'not_done' => 'border-red-700 text-red-700',
        'extra' => 'border-slate-600 text-slate-600',
        default => 'border-stone-400 text-stone-400',
    };
@endphp

<div class="flex items-start gap-3 border-b border-stone-100 py-3 last:border-b-0">
    <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded border text-xs {{ $markClass }}">
        {{ $mark }}
    </span>
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-2">
            <p class="text-sm text-stone-800">{{ $task->title }}</p>
            <x-badge :status="$task->status" />
            @if ($task->is_extra)
                <span class="text-xs text-slate-500">اضافه</span>
            @endif
        </div>
        @if ($task->isNotDone())
            <p class="mt-1 text-xs text-stone-500" title="{{ $task->not_done_note }}">
                دلیل: {{ $task->not_done_reason?->label() }}
                @if ($task->not_done_note)
                    — {{ $task->not_done_note }}
                @endif
            </p>
        @endif
    </div>
</div>
