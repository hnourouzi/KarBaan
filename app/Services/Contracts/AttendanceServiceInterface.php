<?php

namespace App\Services\Contracts;

use App\Models\DailyPlan;
use Illuminate\Support\Carbon;

interface AttendanceServiceInterface
{
    public function start(DailyPlan $plan, Carbon $at): DailyPlan;

    public function finish(DailyPlan $plan, Carbon $at): DailyPlan;

    public function computeHours(DailyPlan $plan): float;
}
