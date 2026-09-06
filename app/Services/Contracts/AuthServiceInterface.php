<?php

namespace App\Services\Contracts;

use App\DTOs\Auth\LoginData;
use App\Models\User;

interface AuthServiceInterface
{
    public function attempt(LoginData $data): User;

    public function logout(): void;

    public function issueToken(User $user, string $deviceName): string;

    public function revokeCurrentToken(User $user): void;
}
