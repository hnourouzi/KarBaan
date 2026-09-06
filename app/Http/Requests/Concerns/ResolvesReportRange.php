<?php

namespace App\Http\Requests\Concerns;

use App\Enums\ReportPeriod;
use App\Services\JalaliDateFormatter;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

trait ResolvesReportRange
{
    protected function convertJalaliRangeInputs(): void
    {
        $formatter = app(JalaliDateFormatter::class);
        $rawFrom = $this->input('from');
        $rawTo = $this->input('to');

        $this->merge([
            'from' => is_string($rawFrom) ? $formatter->tryToGregorianDate($rawFrom) : null,
            'to' => is_string($rawTo) ? $formatter->tryToGregorianDate($rawTo) : null,
            'from_jalali' => is_string($rawFrom) ? $rawFrom : null,
            'to_jalali' => is_string($rawTo) ? $rawTo : null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rangeRules(): array
    {
        return [
            'from' => ['nullable', 'date', 'required_if:period,custom'],
            'to' => ['nullable', 'date', 'required_if:period,custom', 'after_or_equal:from'],
        ];
    }

    /**
     * @return list<callable>
     */
    protected function rangeAfterHooks(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->input('period') !== ReportPeriod::Custom->value) {
                    return;
                }

                if (blank($this->input('from')) || blank($this->input('to'))) {
                    if (filled($this->input('from_jalali')) || filled($this->input('to_jalali'))) {
                        $validator->errors()->add('from', 'بازه شمسی انتخاب‌شده نامعتبر است.');
                    }

                    return;
                }

                $from = Carbon::parse($this->input('from'))->startOfDay();
                $to = Carbon::parse($this->input('to'))->endOfDay();

                if ($from->diffInDays($to) > 366) {
                    $validator->errors()->add('to', 'بازه گزارش نمی‌تواند بیشتر از یک سال باشد.');
                }
            },
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function resolveGregorianRange(): array
    {
        $period = ReportPeriod::from($this->string('period')->toString());
        $formatter = app(JalaliDateFormatter::class);

        return match ($period) {
            ReportPeriod::Daily => [now()->startOfDay(), now()->endOfDay()],
            ReportPeriod::Weekly => $formatter->persianWeekRange(),
            ReportPeriod::Monthly => [now()->startOfMonth(), now()->endOfMonth()],
            ReportPeriod::Custom => [
                Carbon::parse($this->input('from'))->startOfDay(),
                Carbon::parse($this->input('to'))->endOfDay(),
            ],
        };
    }

    public function jalaliFrom(): ?string
    {
        $value = $this->input('from_jalali');

        return filled($value) ? (string) $value : null;
    }

    public function jalaliTo(): ?string
    {
        $value = $this->input('to_jalali');

        return filled($value) ? (string) $value : null;
    }
}
