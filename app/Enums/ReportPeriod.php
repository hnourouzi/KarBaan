<?php

namespace App\Enums;

enum ReportPeriod: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'امروز',
            self::Weekly => 'این هفته',
            self::Monthly => 'این ماه',
            self::Custom => 'بازه سفارشی',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
