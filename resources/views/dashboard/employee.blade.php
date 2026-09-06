<x-layouts.app title="داشبورد | کاربان">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-xl font-semibold text-stone-900">برنامه امروز</h1>
            <p class="text-sm text-stone-500"><x-jalali-date :date="now()" format="full" /></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('history.index') }}" variant="secondary">سوابق من</x-button>
            @if ($plan?->isOpen())
                <x-button href="{{ route('daily-plans.close', $plan) }}" variant="secondary">بستن روز</x-button>
            @elseif (! $plan)
                <x-button href="{{ route('daily-plans.create') }}">ثبت برنامه امروز</x-button>
            @endif
        </div>
    </div>

    @if (! $plan)
        <x-card>
            <p class="text-sm text-stone-600">برای امروز هنوز برنامه‌ای ثبت نشده است. ابتدا فهرست وظایف را مشخص کنید.</p>
        </x-card>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-card title="چک‌لیست وظایف">
                    @forelse ($plan->tasks as $task)
                        <x-checklist-item :task="$task" />
                    @empty
                        <p class="text-sm text-stone-500">وظیفه‌ای ثبت نشده است.</p>
                    @endforelse
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="ساعات کار">
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-stone-500">شروع</dt>
                            <dd><x-jalali-date :date="$plan->started_at" format="time" /></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-stone-500">پایان</dt>
                            <dd><x-jalali-date :date="$plan->closed_at" format="time" /></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-stone-500">ساعت کارکرد</dt>
                            <dd>{{ $plan->hours_worked ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-stone-500">وضعیت</dt>
                            <dd>{{ $plan->status->label() }}</dd>
                        </div>
                    </dl>
                </x-card>

                @if ($plan->isOpen())
                    <x-card title="افزودن وظیفه">
                        <form method="POST" action="{{ route('daily-plans.tasks.store', $plan) }}" class="space-y-3">
                            @csrf
                            <x-input name="title" label="عنوان" required />
                            <label class="flex items-center gap-2 text-sm text-stone-600">
                                <input type="checkbox" name="is_extra" value="1" class="rounded border-stone-300">
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
