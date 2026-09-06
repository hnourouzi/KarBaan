<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthServiceInterface $auth) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->toDto();
        $user = $this->auth->attempt($data);
        $token = $this->auth->issueToken($user, $data->deviceName);

        return ApiResponse::success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => UserResource::make($user)->resolve(),
        ], 'ورود موفقیت‌آمیز بود.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->auth->revokeCurrentToken($request->user());

        return ApiResponse::success(null, 'خروج انجام شد.');
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            UserResource::make($request->user()),
        );
    }
}
