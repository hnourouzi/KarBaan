<x-layouts.app title="گزارش‌ها | کاربان">
    <x-ui.page-header title="گزارش دوره‌ای">
        <x-slot>
            از <x-jalali-date :date="$report->from" /> تا <x-jalali-date :date="$report->to" />
        </x-slot>
    </x-ui.page-header>

    <x-card class="mb-8" title="فیلتر گزارش">
        <form method="GET" action="{{ route('reports.index') }}" class="grid gap-4 md:grid-cols-5">
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
                <x-button class="w-full">اعمال فیلتر</x-button>
            </div>
        </form>
    </x-card>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="نرخ تکمیل" :value="$report->completionRate" suffix="٪" icon="chart" />
        <x-ui.stat-card label="ساعات کار" :value="$report->hoursWorked" icon="clock" />
        <x-ui.stat-card label="انجام‌نشده" :value="$report->notDoneCount" icon="x-circle" />
        <x-ui.stat-card label="اضافه" :value="$report->extraCount" icon="plus-circle" />
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="به تفکیک کارمند">
            <div class="kb-table-wrap">
                <table class="kb-table">
                    <thead>
                        <tr>
                            <th>کارمند</th>
                            <th>برنامه</th>
                            <th>انجام‌شده</th>
                            <th>انجام‌نشده</th>
                            <th>اضافه</th>
                            <th>نرخ</th>
                            <th>ساعات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report->employees as $employee)
                            <tr>
                                <td class="font-medium text-slate-900">{{ $employee->userName }}</td>
                                <td>{{ $employee->plannedCount }}</td>
                                <td>{{ $employee->doneCount }}</td>
                                <td>{{ $employee->notDoneCount }}</td>
                                <td>{{ $employee->extraCount }}</td>
                                <td>{{ $employee->completionRate }}٪</td>
                                <td>{{ $employee->hoursWorked }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <x-ui.empty-state icon="users" title="داده‌ای در این بازه نیست" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="دلایل انجام‌نشدن">
            <ul class="space-y-2 text-sm">
                @forelse ($report->reasonTotals as $reason)
                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2.5">
                        <span class="text-slate-700">{{ $reason->reason->label() }}</span>
                        <span class="font-semibold text-slate-900">{{ $reason->count }}</span>
                    </li>
                @empty
                    <li>
                        <x-ui.empty-state icon="inbox" title="موردی ثبت نشده است" />
                    </li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.app>
