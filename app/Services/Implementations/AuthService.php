<?php

namespace App\Services\Implementations;

use App\DTOs\Auth\LoginData;
use App\Exceptions\InactiveUserException;
use App\Models\User;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService implements AuthServiceInterface
{
    public function attempt(LoginData $data): User
    {
        $user = User::query()->where('email', $data->email)->first();

        if ($user === null || ! Hash::check($data->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'ایمیل یا رمز عبور نادرست است.',
            ]);
        }

        if (! $user->is_active) {
            throw InactiveUserException::make();
        }

        if (! request()->is('api/*')) {
            Auth::login($user, $data->remember);
        }

        return $user;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    public function issueToken(User $user, string $deviceName): string
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    public function revokeCurrentToken(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }
}
