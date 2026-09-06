<x-layouts.app title="گزارش کارمند | کاربان">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-stone-900">گزارش دوره‌ای کارمند</h1>
        @if ($history)
            <p class="text-sm text-stone-500">
                {{ $history->summary->userName }} —
                از <x-jalali-date :date="$history->from" /> تا <x-jalali-date :date="$history->to" />
            </p>
        @else
            <p class="text-sm text-stone-500">یک کارمند و بازه زمانی را انتخاب کنید.</p>
        @endif
    </div>

    <x-card class="mb-6" title="فیلتر">
        <form method="GET" action="{{ route('reports.employee') }}" class="grid gap-3 md:grid-cols-5">
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
                <x-button class="w-full">اعمال</x-button>
            </div>
        </form>
    </x-card>

    @if ($history)
        <div class="mb-4 flex flex-wrap gap-3">
            <x-button
                href="{{ route('reports.employee.export.excel', request()->query()) }}"
                variant="secondary"
            >
                خروجی اکسل
            </x-button>
            <x-button
                href="{{ route('reports.employee.export.pdf', request()->query()) }}"
                variant="secondary"
            >
                خروجی PDF
            </x-button>
        </div>

        <div class="mb-6">
            <x-history-summary :summary="$history->summary" />
        </div>

        <x-card title="جزئیات روزانه">
            <x-history-table :days="$history->days" />
        </x-card>

        <x-day-detail-modal :endpoint="url('/reports/days')" />
    @endif
</x-layouts.app>
