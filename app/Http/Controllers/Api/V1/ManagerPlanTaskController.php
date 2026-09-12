<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanTask\StoreManagerPlanTaskRequest;
use App\Http\Resources\PlanTaskResource;
use App\Http\Responses\ApiResponse;
use App\Models\DailyPlan;
use App\Services\Contracts\TaskCompletionServiceInterface;
use Illuminate\Http\JsonResponse;

class ManagerPlanTaskController extends Controller
{
    public function __construct(private TaskCompletionServiceInterface $tasks) {}

    public function store(StoreManagerPlanTaskRequest $request, DailyPlan $dailyPlan): JsonResponse
    {
        $task = $this->tasks->addManagerTask($dailyPlan, $request->toDto(), $request->user());

        return ApiResponse::success(
            PlanTaskResource::make($task->load('assignedBy')),
            'وظیفه به برنامه کارمند اضافه شد.',
            201,
        );
    }
}
