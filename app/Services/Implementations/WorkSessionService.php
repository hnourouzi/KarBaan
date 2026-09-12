<?php

namespace App\Services\Implementations;

use App\Exceptions\CannotModifyClosedPlanException;
use App\Exceptions\InvalidWorkSessionPeriodException;
use App\Exceptions\OpenWorkSessionExistsException;
use App\Exceptions\WorkSessionAlreadyEndedException;
use App\Exceptions\WorkSessionLimitReachedException;
use App\Models\DailyPlan;
use App\Models\WorkSession;
use App\Services\Contracts\WorkSessionServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WorkSessionService implements WorkSessionServiceInterface
{
    public function start(DailyPlan $plan, Carbon $at): WorkSession
    {
        if ($plan->isClosed()) {
            throw CannotModifyClosedPlanException::make();
        }

        return DB::transaction(function () use ($plan, $at) {
            $locked = DailyPlan::query()
                ->whereKey($plan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->isClosed()) {
                throw CannotModifyClosedPlanException::make();
            }

            if ($locked->workSessions()->whereNull('ended_at')->exists()) {
                throw OpenWorkSessionExistsException::make();
            }

            $limit = (int) config('karbaan.max_work_sessions_per_day', 10);

            if ($locked->workSessions()->count() >= $limit) {
                throw WorkSessionLimitReachedException::make($limit);
            }

            $session = $locked->workSessions()->create([
                'started_at' => $at,
                'ended_at' => null,
            ]);

            if ($locked->started_at === null || $locked->workSessions()->count() === 1) {
                $locked->forceFill(['started_at' => $at])->save();
            }

            return $session->refresh();
        });
    }

    public function end(WorkSession $session, Carbon $at): WorkSession
    {
        $session->loadMissing('dailyPlan');

        if ($session->isClosed()) {
            throw WorkSessionAlreadyEndedException::make();
        }

        if ($session->dailyPlan->isClosed()) {
            throw CannotModifyClosedPlanException::make();
        }

        if ($at->lt($session->started_at)) {
            throw InvalidWorkSessionPeriodException::make();
        }

        return DB::transaction(function () use ($session, $at) {
            $locked = WorkSession::query()
                ->whereKey($session->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->isClosed()) {
                throw WorkSessionAlreadyEndedException::make();
            }

            $locked->forceFill(['ended_at' => $at])->save();

            $plan = DailyPlan::query()
                ->whereKey($locked->daily_plan_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->syncConfirmedHours($plan);

            return $locked->refresh();
        });
    }

    public function endOpenSession(DailyPlan $plan, Carbon $at): ?WorkSession
    {
        $open = $plan->workSessions()
            ->whereNull('ended_at')
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->first();

        if ($open === null) {
            return null;
        }

        return $this->end($open, $at);
    }

    public function listFor(DailyPlan $plan): Collection
    {
        return $plan->workSessions()
            ->orderBy('started_at')
            ->orderBy('id')
            ->get();
    }

    public function confirmedHours(DailyPlan $plan): float
    {
        return round(
            $plan->workSessions
                ->filter(fn (WorkSession $session) => $session->isClosed())
                ->sum(fn (WorkSession $session) => $session->durationHours()),
            2,
        );
    }

    public function totalHoursIncludingOpen(DailyPlan $plan): float
    {
        return round(
            $plan->workSessions->sum(fn (WorkSession $session) => $session->durationHours()),
            2,
        );
    }

    private function syncConfirmedHours(DailyPlan $plan): void
    {
        $plan->load('workSessions');

        $plan->forceFill([
            'hours_worked' => $this->confirmedHours($plan),
        ])->save();
    }
}
