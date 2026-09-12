<?php

namespace App\Exceptions;

class WorkSessionAlreadyEndedException extends DomainException
{
    public static function make(): self
    {
        return new self('این جلسه کاری قبلاً بسته شده است.', 409);
    }
}
