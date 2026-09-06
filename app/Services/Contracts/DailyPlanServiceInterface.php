<?php

namespace App\Services\Contracts;

use App\DTOs\DailyPlan\CloseDailyPlanData;
use App\DTOs\DailyPlan\CreateDailyPlanData;
use App\Models\DailyPlan;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface DailyPlanServiceInterface
{
    public function open(User $user, CreateDailyPlanData $data): DailyPlan;

    public function close(DailyPlan $plan, CloseDailyPlanData $data): DailyPlan;

    public function todayFor(User $user): ?DailyPlan;

    public function findForUserOnDate(User $user, Carbon $date): ?DailyPlan;

    /**
     * @return Collection<int, DailyPlan>
     */
    public function listFor(User $actor, ?int $userId = null): Collection;

    public function openPlanNeedingEndOfDayReminder(User $user): ?DailyPlan;
}
