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
            </x-card>
        </div>
        <x-card title="ساعات کار">
            <dl class="space-y-4 text-sm">
                <div class="flex justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                    <dt class="text-slate-500">شروع</dt>
                    <dd class="font-semibold text-slate-900"><x-jalali-date :date="$plan->started_at" format="time" /></dd>
                </div>
                <div class="flex justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                    <dt class="text-slate-500">پایان</dt>
                    <dd class="font-semibold text-slate-900"><x-jalali-date :date="$plan->closed_at" format="time" /></dd>
                </div>
                <div class="flex justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                    <dt class="text-slate-500">ساعت کارکرد</dt>
                    <dd class="font-semibold text-slate-900">{{ $plan->hours_worked ?? '—' }}</dd>
                </div>
            </dl>
        </x-card>
    </div>
</x-layouts.app>
