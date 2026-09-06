<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanTask\StorePlanTaskRequest;
use App\Http\Requests\PlanTask\UpdateTaskStatusRequest;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Services\Contracts\TaskCompletionServiceInterface;
use Illuminate\Http\RedirectResponse;

class PlanTaskController extends Controller
{
    public function __construct(private TaskCompletionServiceInterface $tasks) {}

    public function store(StorePlanTaskRequest $request, DailyPlan $dailyPlan): RedirectResponse
    {
        $this->tasks->add($dailyPlan, $request->toDto());

        return back()->with('success', 'وظیفه به برنامه اضافه شد.');
    }

    public function updateStatus(UpdateTaskStatusRequest $request, PlanTask $planTask): RedirectResponse
    {
        $this->tasks->updateStatus($planTask, $request->toDto());

        return back()->with('success', 'وضعیت وظیفه به‌روز شد.');
    }
}
