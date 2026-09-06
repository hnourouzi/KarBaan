<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\LoginData;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
            'device_name' => ['sometimes', 'string', 'max:100'],
        ];
    }

    public function toDto(): LoginData
    {
        return new LoginData(
            email: $this->string('email')->lower()->toString(),
            password: $this->string('password')->toString(),
            remember: $this->boolean('remember'),
            deviceName: $this->string('device_name', 'web')->toString(),
        );
    }
}
