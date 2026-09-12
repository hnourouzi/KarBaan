<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanTask\StoreManagerPlanTaskRequest;
use App\Http\Resources\PlanTaskResource;
use App\Http\Responses\ApiResponse;
use App\Models\DailyPlan;
use App\Services\Contracts\TaskCompletionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ManagerPlanTaskController extends Controller
{
    public function __construct(private TaskCompletionServiceInterface $tasks) {}

    public function store(StoreManagerPlanTaskRequest $request, DailyPlan $dailyPlan): JsonResponse|RedirectResponse
    {
        $task = $this->tasks->addManagerTask($dailyPlan, $request->toDto(), $request->user());

        if ($request->expectsJson()) {
            return ApiResponse::success(
                PlanTaskResource::make($task->load('assignedBy')),
                'وظیفه به برنامه کارمند اضافه شد.',
                201,
            );
        }

        return back()->with('success', 'وظیفه به برنامه کارمند اضافه شد.');
    }
}
