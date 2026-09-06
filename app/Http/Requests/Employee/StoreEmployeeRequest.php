<?php

namespace App\Http\Requests\Employee;

use App\DTOs\Employee\CreateEmployeeData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'job_title' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function toDto(): CreateEmployeeData
    {
        return new CreateEmployeeData(
            name: $this->string('name')->toString(),
            email: $this->string('email')->lower()->toString(),
            password: $this->string('password')->toString(),
            role: UserRole::from($this->string('role')->toString()),
            jobTitle: $this->filled('job_title') ? $this->string('job_title')->toString() : null,
            isActive: $this->boolean('is_active', true),
        );
    }
}
