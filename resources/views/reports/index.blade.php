<x-layouts.app title="گزارش‌ها | کاربان">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">گزارش دوره‌ای</h1>
        <p class="text-sm text-stone-500">از <x-jalali-date :date="$report->from" /> تا <x-jalali-date :date="$report->to" /></p>
    </div>

    <x-card class="mb-6" title="فیلتر">
        <form method="GET" action="{{ route('reports.index') }}" class="grid gap-3 md:grid-cols-5">
            <x-select name="period" label="بازه">
                @foreach (\App\Enums\ReportPeriod::cases() as $period)
                    <option value="{{ $period->value }}" @selected(($filters['period'] ?? 'weekly') === $period->value)>{{ $period->label() }}</option>
                @endforeach
            </x-select>
            <x-jalali-input name="from" label="از تاریخ" :value="$filters['from'] ?? ''" />
            <x-jalali-input name="to" label="تا تاریخ" :value="$filters['to'] ?? ''" />
            <x-select name="user_id" label="کارمند">
                <option value="">همه</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </x-select>
            <div class="flex items-end">
                <x-button class="w-full">اعمال</x-button>
            </div>
        </form>
    </x-card>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-card><p class="text-xs text-stone-500">نرخ تکمیل</p><p class="mt-2 text-2xl font-semibold">{{ $report->completionRate }}٪</p></x-card>
        <x-card><p class="text-xs text-stone-500">ساعات کار</p><p class="mt-2 text-2xl font-semibold">{{ $report->hoursWorked }}</p></x-card>
        <x-card><p class="text-xs text-stone-500">انجام‌نشده</p><p class="mt-2 text-2xl font-semibold">{{ $report->notDoneCount }}</p></x-card>
        <x-card><p class="text-xs text-stone-500">اضافه</p><p class="mt-2 text-2xl font-semibold">{{ $report->extraCount }}</p></x-card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="به تفکیک کارمند">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-right text-sm">
                    <thead class="border-b border-stone-200 text-stone-500">
                        <tr>
                            <th class="py-2 font-medium">کارمند</th>
                            <th class="py-2 font-medium">برنامه</th>
                            <th class="py-2 font-medium">انجام‌شده</th>
                            <th class="py-2 font-medium">انجام‌نشده</th>
                            <th class="py-2 font-medium">اضافه</th>
                            <th class="py-2 font-medium">نرخ</th>
                            <th class="py-2 font-medium">ساعات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report->employees as $employee)
                            <tr class="border-b border-stone-100">
                                <td class="py-3">{{ $employee->userName }}</td>
                                <td class="py-3">{{ $employee->plannedCount }}</td>
                                <td class="py-3">{{ $employee->doneCount }}</td>
                                <td class="py-3">{{ $employee->notDoneCount }}</td>
                                <td class="py-3">{{ $employee->extraCount }}</td>
                                <td class="py-3">{{ $employee->completionRate }}٪</td>
                                <td class="py-3">{{ $employee->hoursWorked }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-4 text-stone-500">داده‌ای در این بازه نیست.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="دلایل انجام‌نشدن">
            <ul class="space-y-2 text-sm">
                @forelse ($report->reasonTotals as $reason)
                    <li class="flex justify-between border-b border-stone-100 py-2">
                        <span>{{ $reason->reason->label() }}</span>
                        <span>{{ $reason->count }}</span>
                    </li>
                @empty
                    <li class="text-stone-500">موردی ثبت نشده است.</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
