<x-layouts.app title="داشبورد مدیریت | کاربان">
    <x-ui.page-header title="نمای مدیریتی" description="خلاصه عملکرد هفته جاری" />

    <x-card class="mb-8" title="حضور امروز">
        <p class="-mt-1 mb-5 flex items-center gap-2 text-sm text-slate-500">
            <x-ui.icon name="calendar" class="h-4 w-4" />
            <x-jalali-date :date="now()" format="full" />
        </p>
        <div class="kb-table-wrap">
            <table class="kb-table min-w-[720px]">
                <thead>
                    <tr>
                        <th>کارمند</th>
                        <th>وضعیت</th>
                        <th>شروع</th>
                        <th>پایان</th>
                        <th>وظایف امروز</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($todayAttendance as $row)
                        <tr
                            @class([
                                'cursor-pointer' => $row->dailyPlanId,
                                'opacity-60' => ! $row->dailyPlanId,
                            ])
                            @if ($row->dailyPlanId)
                                data-open-day="{{ $row->dailyPlanId }}"
                                title="مشاهده جزئیات وظایف امروز"
                            @endif
                        >
                            <td class="font-medium text-slate-900">{{ $row->userName }}</td>
                            <td><x-attendance-status-badge :status="$row->status" /></td>
                            <td>
                                @if ($row->startedAt)
                                    <x-jalali-date :date="$row->startedAt" format="time" />
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($row->closedAt)
                                    <x-jalali-date :date="$row->closedAt" format="time" />
                                @elseif ($row->dailyPlanId)
                                    <span class="text-slate-400">هنوز ثبت نشده</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-slate-500">
                                @if ($row->dailyPlanId)
                                    {{ $row->doneCount }} / {{ $row->plannedCount }} انجام‌شده
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-ui.empty-state icon="users" title="کارمندی برای نمایش ثبت نشده است" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat-card label="نرخ تکمیل" :value="$report->completionRate" suffix="٪" icon="chart" />
        <x-ui.stat-card label="ساعت کارکرد" :value="$report->hoursWorked" icon="clock" />
        <x-ui.stat-card :value="$report->doneCount.' / '.$report->plannedCount" label="انجام‌شده / برنامه‌ریزی‌شده" icon="clipboard" />
        <x-ui.stat-card label="وظایف اضافه" :value="$report->extraCount" icon="plus-circle" />
    </div>

    <x-card title="عملکرد کارکنان">
        <div class="kb-table-wrap">
            <table class="kb-table">
                <thead>
                    <tr>
                        <th>کارمند</th>
                        <th>تکمیل</th>
                        <th>انجام‌نشده</th>
                        <th>اضافه</th>
                        <th>ساعات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report->employees as $employee)
                        <tr>
                            <td class="font-medium text-slate-900">{{ $employee->userName }}</td>
                            <td>{{ $employee->completionRate }}٪</td>
                            <td>{{ $employee->notDoneCount }}</td>
                            <td>{{ $employee->extraCount }}</td>
                            <td>{{ $employee->hoursWorked }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-ui.empty-state icon="chart" title="داده‌ای برای این هفته ثبت نشده است" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex flex-wrap gap-2 border-t border-slate-100 pt-5">
            <x-button href="{{ route('reports.employee') }}" variant="secondary">گزارش کارمند</x-button>
            <x-button href="{{ route('reports.index') }}" variant="secondary">گزارش تیم</x-button>
        </div>
    </x-card>

    <x-day-detail-modal :endpoint="url('/reports/days')" />
</x-layouts.app>
