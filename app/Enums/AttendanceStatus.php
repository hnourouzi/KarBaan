<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case NotStarted = 'not_started';
    case Started = 'started';
    case Finished = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'هنوز شروع نکرده',
            self::Started => 'شروع شده',
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
