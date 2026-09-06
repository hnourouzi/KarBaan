<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Contracts\EmployeeServiceInterface;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeServiceInterface $employees) {}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        return ApiResponse::success(
            UserResource::collection($this->employees->paginate()),
        );
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = $this->employees->create($request->toDto());

        return ApiResponse::success(
            UserResource::make($employee),
            'کارمند جدید ثبت شد.',
            201,
        );
    }

    public function show(User $employee): JsonResponse
    {
        $this->authorize('view', $employee);

        return ApiResponse::success(UserResource::make($employee));
    }

    public function update(UpdateEmployeeRequest $request, User $employee): JsonResponse
    {
        $employee = $this->employees->update($employee, $request->toDto());

        return ApiResponse::success(
            UserResource::make($employee),
            'اطلاعات کارمند به‌روز شد.',
        );
    }

    public function destroy(User $employee): JsonResponse
    {
        $this->authorize('delete', $employee);

        $this->employees->delete($employee);

        return ApiResponse::success(null, 'کارمند حذف شد.');
    }
}
