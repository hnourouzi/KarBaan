<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanTask\StorePlanTaskRequest;
use App\Http\Requests\PlanTask\UpdatePlanTaskRequest;
use App\Http\Requests\PlanTask\UpdateTaskStatusRequest;
use App\Http\Resources\PlanTaskResource;
use App\Http\Responses\ApiResponse;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Services\Contracts\TaskCompletionServiceInterface;
use Illuminate\Http\JsonResponse;

class PlanTaskController extends Controller
{
    public function __construct(private TaskCompletionServiceInterface $tasks) {}

    public function store(StorePlanTaskRequest $request, DailyPlan $dailyPlan): JsonResponse
    {
        $task = $this->tasks->add($dailyPlan, $request->toDto());

        return ApiResponse::success(
            PlanTaskResource::make($task),
            'وظیفه اضافه شد.',
            201,
        );
    }

    public function update(UpdatePlanTaskRequest $request, PlanTask $planTask): JsonResponse
    {
        $task = $this->tasks->update($planTask, $request->toDto());

        return ApiResponse::success(
            PlanTaskResource::make($task),
            'وظیفه به‌روز شد.',
        );
    }

    public function updateStatus(UpdateTaskStatusRequest $request, PlanTask $planTask): JsonResponse
    {
        $task = $this->tasks->updateStatus($planTask, $request->toDto());

        return ApiResponse::success(
            PlanTaskResource::make($task),
            'وضعیت وظیفه به‌روز شد.',
        );
    }
}
