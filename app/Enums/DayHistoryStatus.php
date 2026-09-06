<?php

namespace App\Enums;

enum DayHistoryStatus: string
{
    case Closed = 'closed';
    case Open = 'open';
    case None = 'none';

    public function label(): string
    {
        return match ($this) {
            self::Closed => 'بسته‌شده',
            self::Open => 'بسته‌نشده',
            self::None => 'بدون برنامه',
        };
    }
}
