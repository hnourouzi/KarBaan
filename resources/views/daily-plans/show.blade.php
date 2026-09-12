<x-layouts.app title="جزئیات برنامه | کاربان">
    <x-ui.page-header :title="'برنامه '.$plan->user->name">
        <x-slot>
            <x-jalali-date :date="$plan->plan_date" format="full" /> —
            @if ($plan->isClosed())
                <span class="kb-badge kb-badge-success">{{ $plan->status->label() }}</span>
            @else
                <span class="kb-badge kb-badge-warning">{{ $plan->status->label() }}</span>
            @endif
        </x-slot>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-2 lg:col-span-2">
            <x-card title="چک‌لیست">
                <div class="space-y-2">
                    @foreach ($plan->tasks as $task)
                        <x-checklist-item :task="$task" />
                    @endforeach
                </div>
                @can('assignTask', $plan)
                    <form method="POST" action="{{ route('daily-plans.manager-tasks.store', $plan) }}" class="mt-5 space-y-3 border-t border-slate-100 pt-5">
                        @csrf
                        <p class="text-sm font-semibold text-slate-900">افزودن وظیفه</p>
                        <x-input name="title" label="عنوان" required />
                        <x-input name="note" label="یادداشت (اختیاری)" />
                        <x-button size="sm">افزودن وظیفه</x-button>
                    </form>
                @endcan
            </x-card>
        </div>
        <x-card title="ساعات کار">
            <x-work-session-list :plan="$plan" class="text-sm" />
        </x-card>
    </div>
</x-layouts.app>
