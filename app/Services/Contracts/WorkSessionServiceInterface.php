<?php

namespace App\Services\Contracts;

use App\Models\DailyPlan;
use App\Models\WorkSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface WorkSessionServiceInterface
{
    public function start(DailyPlan $plan, Carbon $at): WorkSession;

    public function end(WorkSession $session, Carbon $at): WorkSession;

    public function endOpenSession(DailyPlan $plan, Carbon $at): ?WorkSession;

    /**
     * @return Collection<int, WorkSession>
     */
    public function listFor(DailyPlan $plan): Collection;

    public function confirmedHours(DailyPlan $plan): float;

    public function totalHoursIncludingOpen(DailyPlan $plan): float;
}
