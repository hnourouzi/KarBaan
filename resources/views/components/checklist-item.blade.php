@props(['task'])

@php
    $mark = match ($task->status->value) {
        'done' => ['icon' => 'check-circle', 'class' => 'bg-emerald-50 text-emerald-600 ring-emerald-600/20'],
        'not_done' => ['icon' => 'x-circle', 'class' => 'bg-rose-50 text-rose-600 ring-rose-600/20'],
        'extra' => ['icon' => 'plus-circle', 'class' => 'bg-slate-100 text-slate-600 ring-slate-500/15'],
        default => ['icon' => 'clipboard', 'class' => 'bg-slate-50 text-slate-400 ring-slate-300/50'],
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/40 px-4 py-3.5 transition hover:border-slate-200 hover:bg-white']) }}>
    <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg ring-1 {{ $mark['class'] }}">
        <x-ui.icon :name="$mark['icon']" class="h-4 w-4" />
    </span>
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-2">
            <p class="text-sm font-medium text-slate-900">{{ $task->title }}</p>
            <x-badge :status="$task->status" />
            @if ($task->isAssignedByManager())
                <span class="kb-badge kb-badge-info">توسط مدیر اضافه شده</span>
            @endif
            @if ($task->is_extra)
                <span class="text-xs font-medium text-slate-500">اضافه</span>
            @endif
        </div>
        @if ($task->isNotDone())
            <p class="mt-1.5 text-xs text-slate-500" title="{{ $task->not_done_note }}">
                دلیل: {{ $task->not_done_reason?->label() }}
                @if ($task->not_done_note)
                    — {{ $task->not_done_note }}
                @endif
            </p>
        @endif
    </div>
</div>
