<?php

namespace App\Enums;

enum UserRole: string
{
    case Employee = 'employee';
    case Manager = 'manager';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Employee => 'کارمند',
            self::Manager => 'مدیر',
            self::Admin => 'ادمین',
        };
    }

    public function canManageTeam(): bool
    {
        return $this === self::Manager || $this === self::Admin;
    }

    public function canManageEmployees(): bool
    {
        return $this === self::Admin;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
