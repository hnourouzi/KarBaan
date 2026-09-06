@props(['days'])

<div class="overflow-x-auto">
    <table class="w-full min-w-[720px] text-right text-sm">
        <thead class="border-b border-stone-200 text-stone-500">
            <tr>
                <th class="py-2 font-medium">تاریخ</th>
                <th class="py-2 font-medium">روز</th>
                <th class="py-2 font-medium">ساعات</th>
                <th class="py-2 font-medium">برنامه</th>
                <th class="py-2 font-medium">انجام‌شده</th>
                <th class="py-2 font-medium">انجام‌نشده</th>
                <th class="py-2 font-medium">اضافه</th>
                <th class="py-2 font-medium">وضعیت</th>
                <th class="py-2 font-medium"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($days as $day)
                <tr class="border-b border-stone-100">
                    <td class="py-3"><x-jalali-date :date="$day->date" /></td>
                    <td class="py-3"><x-jalali-date :date="$day->date" format="day" /></td>
                    <td class="py-3">{{ $day->hoursWorked }}</td>
                    <td class="py-3">{{ $day->plannedCount }}</td>
                    <td class="py-3">{{ $day->doneCount }}</td>
                    <td class="py-3">{{ $day->notDoneCount }}</td>
                    <td class="py-3">{{ $day->extraCount }}</td>
                    <td class="py-3"><x-day-status-badge :status="$day->status" /></td>
                    <td class="py-3">
                        @if ($day->planId)
                            <button
                                type="button"
                                data-open-day="{{ $day->planId }}"
                                class="inline-flex items-center justify-center rounded-md border border-stone-300 bg-white px-3 py-1.5 text-sm font-medium text-stone-800 hover:bg-stone-50"
                            >
                                مشاهده کارها
                            </button>
                        @else
                            <span class="text-xs text-stone-400">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="py-4 text-stone-500">در این بازه روزی ثبت نشده است.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
