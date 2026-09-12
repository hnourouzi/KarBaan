<x-layouts.app title="داشبورد | کاربان">
    <x-ui.page-header title="برنامه امروز">
        <x-slot>
            <span class="inline-flex items-center gap-2">
                <x-ui.icon name="calendar" class="h-4 w-4" />
                <x-jalali-date :date="now()" format="full" />
            </span>
        </x-slot>
        <x-slot:actions>
            <x-button href="{{ route('history.index') }}" variant="secondary" size="sm">سوابق من</x-button>
            @if ($plan?->isOpen())
                <x-button href="{{ route('daily-plans.close', $plan) }}" variant="secondary" size="sm">بستن روز</x-button>
            @elseif (! $plan)
                <x-button href="{{ route('daily-plans.create') }}" size="sm">ثبت برنامه امروز</x-button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    @if (! $plan)
        <x-card>
            <x-ui.empty-state
                icon="clipboard"
                title="برای امروز هنوز برنامه‌ای ثبت نشده است"
                description="ابتدا فهرست وظایف امروز را مشخص کنید تا پیگیری و ثبت ساعات کار آغاز شود."
            >
                <x-button href="{{ route('daily-plans.create') }}">شروع برنامه‌ریزی</x-button>
            </x-ui.empty-state>
        </x-card>
    @else
        <x-manager-task-banner :plan="$plan" />

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-3 lg:col-span-2">
                <x-card title="چک‌لیست وظایف">
                    <div class="space-y-2">
                        @forelse ($plan->tasks as $task)
                            <x-checklist-item :task="$task" />
                        @empty
                            <x-ui.empty-state icon="clipboard" title="وظیفه‌ای ثبت نشده است" />
                        @endforelse
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="ساعات کار">
                    <x-work-session-list :plan="$plan" class="text-sm" />
                    <div class="mt-4 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5 text-sm">
                        <dt class="font-medium text-slate-500">وضعیت روز</dt>
                        <dd>
                            @if ($plan->isClosed())
                                <span class="kb-badge kb-badge-success">{{ $plan->status->label() }}</span>
                            @else
                                <span class="kb-badge kb-badge-warning">{{ $plan->status->label() }}</span>
                            @endif
                        </dd>
                    </div>
                </x-card>

                @if ($plan->isOpen())
                    <x-card title="افزودن وظیفه">
                        <form method="POST" action="{{ route('daily-plans.tasks.store', $plan) }}" class="space-y-4">
                            @csrf
                            <x-input name="title" label="عنوان" required />
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="is_extra" value="1" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/20">
                                وظیفه اضافه / خارج از برنامه
                            </label>
                            <x-button class="w-full">افزودن</x-button>
                        </form>
                    </x-card>
                @endif
            </div>
        </div>
    @endif
</x-layouts.app>
