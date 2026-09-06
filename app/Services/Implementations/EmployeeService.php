<?php

namespace App\Services\Implementations;

use App\DTOs\Employee\CreateEmployeeData;
use App\DTOs\Employee\UpdateEmployeeData;
use App\Models\User;
use App\Services\Contracts\EmployeeServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EmployeeService implements EmployeeServiceInterface
{
    public function paginate(): LengthAwarePaginator
    {
        return User::query()
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(15);
    }

    public function create(CreateEmployeeData $data): User
    {
        return User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
            'role' => $data->role,
            'job_title' => $data->jobTitle,
            'is_active' => $data->isActive,
        ]);
    }

    public function update(User $employee, UpdateEmployeeData $data): User
    {
        $attributes = [
            'name' => $data->name,
            'email' => $data->email,
            'role' => $data->role,
            'job_title' => $data->jobTitle,
            'is_active' => $data->isActive,
        ];

        if ($data->password !== null) {
            $attributes['password'] = $data->password;
        }

        $employee->update($attributes);

        return $employee->refresh();
    }

    public function delete(User $employee): void
    {
        $employee->delete();
    }
}
