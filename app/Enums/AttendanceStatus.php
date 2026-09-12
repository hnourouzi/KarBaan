<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case NotStarted = 'not_started';
    case Started = 'started';
    case OnBreak = 'on_break';
    case Finished = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'شروع نشده',
            self::Started => 'در حال کار',
            self::OnBreak => 'بین دو شیفت',
            self::Finished => 'پایان یافته',
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
