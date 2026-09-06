<x-layouts.app title="داشبورد مدیریت | کاربان">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">نمای مدیریتی</h1>
        <p class="text-sm text-stone-500">خلاصه عملکرد هفته جاری</p>
    </div>

    <x-card class="mb-6" title="حضور امروز">
        <p class="-mt-2 mb-4 text-sm text-stone-500">
            <x-jalali-date :date="now()" format="full" />
        </p>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-right text-sm">
                <thead class="border-b border-stone-200 text-stone-500">
                    <tr>
                        <th class="py-2 font-medium">کارمند</th>
                        <th class="py-2 font-medium">وضعیت</th>
                        <th class="py-2 font-medium">شروع</th>
                        <th class="py-2 font-medium">پایان</th>
                        <th class="py-2 font-medium">وظایف امروز</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($todayAttendance as $row)
                        <tr
                            @class([
                                'border-b border-stone-100',
                                'cursor-pointer hover:bg-stone-50' => $row->dailyPlanId,
                                'text-stone-400' => ! $row->dailyPlanId,
                            ])
                            @if ($row->dailyPlanId)
                                data-open-day="{{ $row->dailyPlanId }}"
                                title="مشاهده جزئیات وظایف امروز"
                            @endif
                        >
                            <td class="py-3">{{ $row->userName }}</td>
                            <td class="py-3">
                                <x-attendance-status-badge :status="$row->status" />
                            </td>
                            <td class="py-3">
                                @if ($row->startedAt)
                                    <x-jalali-date :date="$row->startedAt" format="time" />
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3">
                                @if ($row->closedAt)
                                    <x-jalali-date :date="$row->closedAt" format="time" />
                                @elseif ($row->dailyPlanId)
                                    <span class="text-stone-400">هنوز ثبت نشده</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 text-stone-500">
                                @if ($row->dailyPlanId)
                                    {{ $row->doneCount }} / {{ $row->plannedCount }} انجام‌شده
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-stone-500">کارمندی برای نمایش ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-xs text-stone-500">نرخ تکمیل</p>
            <p class="mt-2 text-2xl font-semibold">{{ $report->completionRate }}٪</p>
        </x-card>
        <x-card>
            <p class="text-xs text-stone-500">ساعت کارکرد</p>
            <p class="mt-2 text-2xl font-semibold">{{ $report->hoursWorked }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-stone-500">انجام‌شده / برنامه‌ریزی‌شده</p>
            <p class="mt-2 text-2xl font-semibold">{{ $report->doneCount }} / {{ $report->plannedCount }}</p>
        </x-card>
        <x-card>
            <p class="text-xs text-stone-500">وظایف اضافه</p>
            <p class="mt-2 text-2xl font-semibold">{{ $report->extraCount }}</p>
        </x-card>
    </div>

    <x-card title="عملکرد کارکنان">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-right text-sm">
                <thead class="border-b border-stone-200 text-stone-500">
                    <tr>
                        <th class="py-2 font-medium">کارمند</th>
                        <th class="py-2 font-medium">تکمیل</th>
                        <th class="py-2 font-medium">انجام‌نشده</th>
                        <th class="py-2 font-medium">اضافه</th>
                        <th class="py-2 font-medium">ساعات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report->employees as $employee)
                        <tr class="border-b border-stone-100">
                            <td class="py-3">{{ $employee->userName }}</td>
                            <td class="py-3">{{ $employee->completionRate }}٪</td>
                            <td class="py-3">{{ $employee->notDoneCount }}</td>
                            <td class="py-3">{{ $employee->extraCount }}</td>
                            <td class="py-3">{{ $employee->hoursWorked }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-stone-500">داده‌ای برای این هفته ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <x-button href="{{ route('reports.employee') }}" variant="secondary">گزارش کارمند</x-button>
            <x-button href="{{ route('reports.index') }}" variant="secondary">گزارش تیم</x-button>
        </div>
    </x-card>

    <x-day-detail-modal :endpoint="url('/reports/days')" />
</x-layouts.app>
