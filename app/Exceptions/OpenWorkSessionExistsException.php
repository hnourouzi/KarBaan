<?php

namespace App\Exceptions;

class OpenWorkSessionExistsException extends DomainException
{
    public static function make(): self
    {
        return new self('ابتدا جلسه کاری جاری را ببندید و بعد جلسه جدید را شروع کنید.', 409);
    }
}
