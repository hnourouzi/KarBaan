<?php

namespace App\Exceptions;

class WorkSessionLimitReachedException extends DomainException
{
    public static function make(int $limit): self
    {
        return new self("در یک روز حداکثر {$limit} جلسه کاری می‌توان ثبت کرد.", 422);
    }
}
