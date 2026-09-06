<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Planned = 'planned';
    case Done = 'done';
    case NotDone = 'not_done';
    case Extra = 'extra';

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'برنامه‌ریزی‌شده',
            self::Done => 'انجام‌شده',
            self::NotDone => 'انجام‌نشده',
            self::Extra => 'اضافه',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return list<self>
     */
    public static function closeoutStatuses(): array
    {
        return [self::Done, self::NotDone];
    }
}
