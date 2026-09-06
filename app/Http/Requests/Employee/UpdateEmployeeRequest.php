<?php

namespace App\Http\Requests\Employee;

use App\DTOs\Employee\UpdateEmployeeData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User $employee */
        $employee = $this->route('employee');

        return $this->user()?->can('update', $employee) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $employee */
        $employee = $this->route('employee');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($employee)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'job_title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function toDto(): UpdateEmployeeData
    {
        return new UpdateEmployeeData(
            name: $this->string('name')->toString(),
            email: $this->string('email')->lower()->toString(),
            role: UserRole::from($this->string('role')->toString()),
            jobTitle: $this->filled('job_title') ? $this->string('job_title')->toString() : null,
            isActive: $this->boolean('is_active', true),
            password: $this->filled('password') ? $this->string('password')->toString() : null,
        );
    }
}
