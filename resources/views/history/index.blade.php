<x-layouts.app title="سوابق کاری من | کاربان">
    <x-ui.page-header title="سوابق کاری من">
        <x-slot>
            از <x-jalali-date :date="$history->from" /> تا <x-jalali-date :date="$history->to" />
        </x-slot>
    </x-ui.page-header>

    <x-card class="mb-8" title="فیلتر بازه">
        <form method="GET" action="{{ route('history.index') }}" class="grid gap-4 md:grid-cols-4">
            <x-select name="period" label="بازه">
                @foreach (\App\Enums\ReportPeriod::cases() as $period)
                    <option value="{{ $period->value }}" @selected(($filters['period'] ?? 'monthly') === $period->value)>{{ $period->label() }}</option>
                @endforeach
            </x-select>
            <x-jalali-input name="from" label="از تاریخ" :value="$filters['from'] ?? ''" />
            <x-jalali-input name="to" label="تا تاریخ" :value="$filters['to'] ?? ''" />
            <div class="flex items-end">
                <x-button class="w-full">اعمال فیلتر</x-button>
            </div>
        </form>
    </x-card>

    <div class="mb-8">
        <x-history-summary :summary="$history->summary" />
    </div>

    <x-card title="روزهای کاری">
        <x-history-table :days="$history->days" />
    </x-card>

    <x-day-detail-modal :endpoint="url('/reports/days')" />
</x-layouts.app>
