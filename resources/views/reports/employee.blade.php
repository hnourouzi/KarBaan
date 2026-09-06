<x-layouts.app title="گزارش کارمند | کاربان">
    <x-ui.page-header title="گزارش دوره‌ای کارمند">
        <x-slot>
            @if ($history)
                {{ $history->summary->userName }} —
                از <x-jalali-date :date="$history->from" /> تا <x-jalali-date :date="$history->to" />
            @else
                یک کارمند و بازه زمانی را انتخاب کنید.
            @endif
        </x-slot>
    </x-ui.page-header>

    <x-card class="mb-8" title="فیلتر گزارش">
        <form method="GET" action="{{ route('reports.employee') }}" class="grid gap-4 md:grid-cols-5">
            <x-select name="user_id" label="کارمند">
                <option value="">انتخاب کنید</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" @selected((string) ($filters['user_id'] ?? '') === (string) $employee->id)>{{ $employee->name }}</option>
                @endforeach
            </x-select>
            <x-select name="period" label="بازه">
                @foreach (\App\Enums\ReportPeriod::cases() as $period)
                    <option value="{{ $period->value }}" @selected(($filters['period'] ?? 'weekly') === $period->value)>{{ $period->label() }}</option>
                @endforeach
            </x-select>
            <x-jalali-input name="from" label="از تاریخ" :value="$filters['from'] ?? ''" />
            <x-jalali-input name="to" label="تا تاریخ" :value="$filters['to'] ?? ''" />
            <div class="flex items-end">
                <x-button class="w-full">اعمال فیلتر</x-button>
            </div>
        </form>
    </x-card>

    @if ($history)
        <div class="mb-6 flex flex-wrap gap-2">
            <x-button href="{{ route('reports.employee.export.excel', request()->query()) }}" variant="secondary" size="sm">
                خروجی اکسل
            </x-button>
            <x-button href="{{ route('reports.employee.export.pdf', request()->query()) }}" variant="secondary" size="sm">
                خروجی PDF
            </x-button>
        </div>

        <div class="mb-8">
            <x-history-summary :summary="$history->summary" />
        </div>

        <x-card title="جزئیات روزانه">
            <x-history-table :days="$history->days" />
        </x-card>

        <x-day-detail-modal :endpoint="url('/reports/days')" />
    @else
        <x-card>
            <x-ui.empty-state
                icon="document"
                title="گزارشی برای نمایش انتخاب نشده است"
                description="کارمند و بازه زمانی را در فیلتر بالا مشخص کنید."
            />
        </x-card>
    @endif
</x-layouts.app>
