<?php

namespace App\Exceptions;

class CannotModifyClosedPlanException extends DomainException
{
    public static function make(): self
    {
        return new self('برنامه بسته‌شده قابل ویرایش نیست.', 409);
    }
}
