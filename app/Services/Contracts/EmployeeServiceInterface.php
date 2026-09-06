<?php

namespace App\Services\Contracts;

use App\DTOs\Employee\CreateEmployeeData;
use App\DTOs\Employee\UpdateEmployeeData;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EmployeeServiceInterface
{
    public function paginate(): LengthAwarePaginator;

    public function create(CreateEmployeeData $data): User;

    public function update(User $employee, UpdateEmployeeData $data): User;

    public function delete(User $employee): void;
}
