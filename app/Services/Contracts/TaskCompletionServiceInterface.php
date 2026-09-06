<?php

namespace App\Services\Contracts;

use App\DTOs\DailyPlan\CloseDailyPlanData;
use App\DTOs\PlanTask\CreatePlanTaskData;
use App\DTOs\PlanTask\UpdatePlanTaskData;
use App\DTOs\PlanTask\UpdateTaskStatusData;
use App\Models\DailyPlan;
use App\Models\PlanTask;

interface TaskCompletionServiceInterface
{
    public function add(DailyPlan $plan, CreatePlanTaskData $data): PlanTask;

    public function update(PlanTask $task, UpdatePlanTaskData $data): PlanTask;

    public function updateStatus(PlanTask $task, UpdateTaskStatusData $data): PlanTask;

    public function applyCloseout(DailyPlan $plan, CloseDailyPlanData $data): void;
}
