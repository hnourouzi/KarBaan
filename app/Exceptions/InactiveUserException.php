<?php

namespace App\Exceptions;

class InactiveUserException extends DomainException
{
    public static function make(): self
    {
        return new self('حساب کاربری غیرفعال است.', 403);
    }
}
