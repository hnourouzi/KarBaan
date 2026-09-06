<?php

namespace App\Exceptions;

class DailyPlanAlreadyExistsException extends DomainException
{
    public static function forDate(string $date): self
    {
        return new self("برای تاریخ {$date} قبلاً برنامه روزانه ثبت شده است.", 409);
    }
}
