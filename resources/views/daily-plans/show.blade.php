<x-layouts.app title="جزئیات برنامه | کاربان">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">برنامه {{ $plan->user->name }}</h1>
        <p class="text-sm text-stone-500"><x-jalali-date :date="$plan->plan_date" format="full" /> — {{ $plan->status->label() }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <x-card title="چک‌لیست">
                @foreach ($plan->tasks as $task)
                    <x-checklist-item :task="$task" />
                @endforeach
            </x-card>
        </div>
        <x-card title="ساعات کار">
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-stone-500">شروع</dt><dd><x-jalali-date :date="$plan->started_at" format="time" /></dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">پایان</dt><dd><x-jalali-date :date="$plan->closed_at" format="time" /></dd></div>
                <div class="flex justify-between"><dt class="text-stone-500">ساعت کارکرد</dt><dd>{{ $plan->hours_worked ?? '—' }}</dd></div>
            </dl>
        </x-card>
    </div>
</x-layouts.app>
