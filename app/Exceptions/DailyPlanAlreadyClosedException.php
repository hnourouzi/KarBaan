<?php

namespace App\Exceptions;

class DailyPlanAlreadyClosedException extends DomainException
{
    public static function make(): self
    {
        return new self('این برنامه روزانه قبلاً بسته شده است.', 409);
    }
}
