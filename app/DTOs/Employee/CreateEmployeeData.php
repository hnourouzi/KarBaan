<?php

namespace App\DTOs\Employee;

use App\Enums\UserRole;

readonly class CreateEmployeeData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public UserRole $role,
        public ?string $jobTitle = null,
        public bool $isActive = true,
    ) {}
}
