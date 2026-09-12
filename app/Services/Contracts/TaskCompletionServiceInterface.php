<?php

namespace App\Services\Contracts;

use App\DTOs\DailyPlan\CloseDailyPlanData;
use App\DTOs\PlanTask\CreatePlanTaskData;
use App\DTOs\PlanTask\ManagerTaskData;
use App\DTOs\PlanTask\UpdatePlanTaskData;
use App\DTOs\PlanTask\UpdateTaskStatusData;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;

interface TaskCompletionServiceInterface
{
    public function add(DailyPlan $plan, CreatePlanTaskData $data): PlanTask;

    public function addManagerTask(DailyPlan $plan, ManagerTaskData $data, User $manager): PlanTask;

    public function update(PlanTask $task, UpdatePlanTaskData $data): PlanTask;

    public function updateStatus(PlanTask $task, UpdateTaskStatusData $data): PlanTask;

    public function applyCloseout(DailyPlan $plan, CloseDailyPlanData $data): void;
}
