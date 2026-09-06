@props(['days'])

<div class="kb-table-wrap">
    <table class="kb-table min-w-[720px]">
        <thead>
            <tr>
                <th>تاریخ</th>
                <th>روز</th>
                <th>ساعات</th>
                <th>برنامه</th>
                <th>انجام‌شده</th>
                <th>انجام‌نشده</th>
                <th>اضافه</th>
                <th>وضعیت</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($days as $day)
                <tr>
                    <td class="font-medium text-slate-900"><x-jalali-date :date="$day->date" /></td>
                    <td><x-jalali-date :date="$day->date" format="day" /></td>
                    <td>{{ $day->hoursWorked }}</td>
                    <td>{{ $day->plannedCount }}</td>
                    <td>{{ $day->doneCount }}</td>
                    <td>{{ $day->notDoneCount }}</td>
                    <td>{{ $day->extraCount }}</td>
                    <td><x-day-status-badge :status="$day->status" /></td>
                    <td>
                        @if ($day->planId)
                            <x-button type="button" data-open-day="{{ $day->planId }}" variant="secondary" size="sm">
                                مشاهده کارها
                            </x-button>
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <x-ui.empty-state
                            icon="calendar"
                            title="در این بازه روزی ثبت نشده است"
                            description="بازه زمانی دیگری را انتخاب کنید یا پس از ثبت برنامه روزانه دوباره بررسی کنید."
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
