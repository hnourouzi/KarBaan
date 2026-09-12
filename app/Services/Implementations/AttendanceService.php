<?php

namespace App\Services\Implementations;

use App\Models\DailyPlan;
use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\WorkSessionServiceInterface;
use Illuminate\Support\Carbon;

class AttendanceService implements AttendanceServiceInterface
{
    public function __construct(private WorkSessionServiceInterface $sessions) {}

    public function start(DailyPlan $plan, Carbon $at): DailyPlan
    {
        $plan->forceFill([
            'started_at' => $at,
        ])->save();

        if ($plan->workSessions()->doesntExist()) {
            $this->sessions->start($plan->refresh(), $at);
        }

        return $plan->refresh();
    }

    public function finish(DailyPlan $plan, Carbon $at): DailyPlan
    {
        $plan->loadMissing('workSessions');

        $this->sessions->endOpenSession($plan, $at);

        $plan->refresh()->load('workSessions');

        $plan->forceFill([
            'closed_at' => $at,
            'hours_worked' => $this->sessions->confirmedHours($plan),
        ])->save();

        return $plan->refresh();
    }

    public function computeHours(DailyPlan $plan): float
    {
        $plan->loadMissing('workSessions');

        return $this->sessions->confirmedHours($plan);
    }
}
