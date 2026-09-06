<?php

namespace App\DTOs\Employee;

use App\Enums\UserRole;

readonly class UpdateEmployeeData
{
    public function __construct(
        public string $name,
        public string $email,
        public UserRole $role,
        public ?string $jobTitle = null,
        public bool $isActive = true,
        public ?string $password = null,
    ) {}
}
