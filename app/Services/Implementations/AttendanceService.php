<?php

namespace App\Services\Implementations;

use App\Models\DailyPlan;
use App\Services\Contracts\AttendanceServiceInterface;
use Illuminate\Support\Carbon;

class AttendanceService implements AttendanceServiceInterface
{
    public function start(DailyPlan $plan, Carbon $at): DailyPlan
    {
        $plan->forceFill([
            'started_at' => $at,
        ])->save();

        return $plan;
    }

    public function finish(DailyPlan $plan, Carbon $at): DailyPlan
    {
        $plan->forceFill([
            'closed_at' => $at,
            'hours_worked' => $this->hoursBetween($plan->started_at, $at),
        ])->save();

        return $plan->refresh();
    }

    public function computeHours(DailyPlan $plan): float
    {
        if ($plan->closed_at === null) {
            return $this->hoursBetween($plan->started_at, now());
        }

        return $this->hoursBetween($plan->started_at, $plan->closed_at);
    }

    private function hoursBetween(Carbon $from, Carbon $to): float
    {
        $minutes = abs($from->diffInMinutes($to));

        return round($minutes / 60, 2);
    }
}
