@props(['summary'])

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <x-ui.stat-card label="ساعات کار انجام‌شده" :value="$summary->hoursWorked" suffix="ساعت" icon="clock" />
    <x-ui.stat-card label="کل تسک‌ها" :value="$summary->plannedCount" icon="clipboard" />
    <x-ui.stat-card label="انجام‌شده" :value="$summary->doneCount" icon="check-circle" />
    <x-ui.stat-card label="انجام‌نشده" :value="$summary->notDoneCount" icon="x-circle" />
    <x-ui.stat-card label="تسک‌های اضافه" :value="$summary->extraCount" icon="plus-circle" />
    <x-ui.stat-card label="درصد تکمیل" :value="$summary->completionRate" suffix="٪" icon="chart" />
</div>
