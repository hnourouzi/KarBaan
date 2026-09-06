@props(['summary'])

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <x-card>
        <p class="text-xs text-stone-500">ساعات کار انجام‌شده</p>
        <p class="mt-2 text-2xl font-semibold">{{ $summary->hoursWorked }} ساعت</p>
    </x-card>
    <x-card>
        <p class="text-xs text-stone-500">کل تسک‌ها</p>
        <p class="mt-2 text-2xl font-semibold">{{ $summary->plannedCount }}</p>
    </x-card>
    <x-card>
        <p class="text-xs text-stone-500">انجام‌شده</p>
        <p class="mt-2 text-2xl font-semibold">{{ $summary->doneCount }}</p>
    </x-card>
    <x-card>
        <p class="text-xs text-stone-500">انجام‌نشده</p>
        <p class="mt-2 text-2xl font-semibold">{{ $summary->notDoneCount }}</p>
    </x-card>
    <x-card>
        <p class="text-xs text-stone-500">تسک‌های اضافه</p>
        <p class="mt-2 text-2xl font-semibold">{{ $summary->extraCount }}</p>
    </x-card>
    <x-card>
        <p class="text-xs text-stone-500">درصد تکمیل</p>
        <p class="mt-2 text-2xl font-semibold">{{ $summary->completionRate }}٪</p>
    </x-card>
</div>
