<?php

namespace App\DTOs\Auth;

readonly class LoginData
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember = false,
        public string $deviceName = 'web',
    ) {}
}
