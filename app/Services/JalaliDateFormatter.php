<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Morilog\Jalali\Jalalian;

class JalaliDateFormatter
{
    public const INPUT_FORMAT = 'Y/m/d';

    public function date(CarbonInterface|string|null $date): string
    {
        $carbon = $this->carbon($date);

        return $carbon === null ? '—' : Jalalian::fromCarbon($carbon)->format('Y/m/d');
    }

    public function full(CarbonInterface|string|null $date): string
    {
        $carbon = $this->carbon($date);

        return $carbon === null ? '—' : Jalalian::fromCarbon($carbon)->format('%A %d %B %Y');
    }

    public function dayName(CarbonInterface|string|null $date): string
    {
        $carbon = $this->carbon($date);

        return $carbon === null ? '—' : Jalalian::fromCarbon($carbon)->format('%A');
    }

    public function time(CarbonInterface|string|null $date): string
    {
        $carbon = $this->carbon($date);

        return $carbon === null ? '—' : $carbon->format('H:i');
    }

    public function toGregorianDate(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            throw new InvalidArgumentException('تاریخ خالی است.');
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $value;
        }

        $normalized = str_replace(['-', '.'], '/', $value);

        try {
            return Jalalian::fromFormat(self::INPUT_FORMAT, $normalized)->toCarbon()->toDateString();
        } catch (\Throwable $exception) {
            throw new InvalidArgumentException('تاریخ شمسی نامعتبر است.', previous: $exception);
        }
    }

    public function tryToGregorianDate(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return $this->toGregorianDate($value);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public function persianWeekRange(?CarbonInterface $around = null): array
    {
        $around = Carbon::parse($around ?? now())->timezone(config('app.timezone'));

        return [
            $around->copy()->startOfWeek(Carbon::SATURDAY),
            $around->copy()->endOfWeek(Carbon::FRIDAY),
        ];
    }

    private function carbon(CarbonInterface|string|null $date): ?Carbon
    {
        if ($date === null || $date === '') {
            return null;
        }

        return Carbon::parse($date);
    }
}
