<?php

namespace App\Policies;

use App\Models\PlanTask;
use App\Models\User;

class PlanTaskPolicy
{
    public function view(User $user, PlanTask $planTask): bool
    {
        $planTask->loadMissing('dailyPlan');

        return $user->canManageTeam() || $user->id === $planTask->dailyPlan->user_id;
    }

    public function update(User $user, PlanTask $planTask): bool
    {
        $planTask->loadMissing('dailyPlan');

        return $user->id === $planTask->dailyPlan->user_id && $planTask->dailyPlan->isOpen();
    }

    public function delete(User $user, PlanTask $planTask): bool
    {
        $planTask->loadMissing('dailyPlan');

        return $user->id === $planTask->dailyPlan->user_id && $planTask->dailyPlan->isOpen();
    }
}
