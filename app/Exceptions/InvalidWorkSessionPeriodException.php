<?php

namespace App\Exceptions;

class InvalidWorkSessionPeriodException extends DomainException
{
    public static function make(): self
    {
        return new self('زمان پایان جلسه باید بعد از زمان شروع آن باشد.', 422);
    }
}
