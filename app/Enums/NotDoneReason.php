<?php

namespace App\Enums;

enum NotDoneReason: string
{
    case TimeShortage = 'time_shortage';
    case Blocked = 'blocked';
    case PriorityChange = 'priority_change';
    case WaitingOnOthers = 'waiting_on_others';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::TimeShortage => 'کمبود زمان',
            self::Blocked => 'مانع یا مسدود',
            self::PriorityChange => 'تغییر اولویت',
            self::WaitingOnOthers => 'منتظر دیگران',
            self::Other => 'سایر',
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
