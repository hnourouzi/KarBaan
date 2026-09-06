<?php

namespace App\Services\Implementations;

use App\DTOs\DailyPlan\CloseDailyPlanData;
use App\DTOs\DailyPlan\CreateDailyPlanData;
use App\Enums\DailyPlanStatus;
use App\Enums\TaskStatus;
use App\Exceptions\DailyPlanAlreadyClosedException;
use App\Exceptions\DailyPlanAlreadyExistsException;
use App\Models\DailyPlan;
use App\Models\User;
use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\DailyPlanServiceInterface;
use App\Services\Contracts\TaskCompletionServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DailyPlanService implements DailyPlanServiceInterface
{
    public function __construct(
        private AttendanceServiceInterface $attendance,
        private TaskCompletionServiceInterface $tasks,
    ) {}

    public function open(User $user, CreateDailyPlanData $data): DailyPlan
    {
        $existing = $this->findForUserOnDate($user, $data->planDate);

        if ($existing !== null) {
            throw DailyPlanAlreadyExistsException::forDate($data->planDate->toDateString());
        }

        return DB::transaction(function () use ($user, $data) {
            $plan = DailyPlan::query()->create([
                'user_id' => $user->id,
                'plan_date' => $data->planDate,
                'status' => DailyPlanStatus::Open,
                'started_at' => now(),
                'notes' => $data->notes,
            ]);

            $this->attendance->start($plan, $plan->started_at);

            foreach (array_values($data->taskTitles) as $index => $title) {
                $plan->tasks()->create([
                    'title' => $title,
                    'status' => TaskStatus::Planned,
                    'is_extra' => false,
                    'position' => $index + 1,
                ]);
            }

            return $plan->load(['tasks', 'user']);
        });
    }

    public function close(DailyPlan $plan, CloseDailyPlanData $data): DailyPlan
    {
        if ($plan->isClosed()) {
            throw DailyPlanAlreadyClosedException::make();
        }

        return DB::transaction(function () use ($plan, $data) {
            $locked = DailyPlan::query()
                ->whereKey($plan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($locked->isClosed()) {
                throw DailyPlanAlreadyClosedException::make();
            }

            $this->tasks->applyCloseout($locked, $data);
            $this->attendance->finish($locked, now());

            $locked->forceFill([
                'status' => DailyPlanStatus::Closed,
                'notes' => $data->notes ?? $locked->notes,
            ])->save();

            return $locked->refresh()->load(['tasks', 'user']);
        });
    }

    public function todayFor(User $user): ?DailyPlan
    {
        return $this->findForUserOnDate($user, now());
    }

    public function findForUserOnDate(User $user, Carbon $date): ?DailyPlan
    {
        return DailyPlan::query()
            ->whereBelongsTo($user)
            ->forDate($date)
            ->with(['tasks', 'user'])
            ->first();
    }

    public function listFor(User $actor, ?int $userId = null): Collection
    {
        $query = DailyPlan::query()
            ->with(['user', 'tasks'])
            ->orderByDesc('plan_date')
            ->orderByDesc('id');

        if ($actor->canManageTeam() && $userId !== null) {
            $query->where('user_id', $userId);
        } elseif (! $actor->canManageTeam()) {
            $query->whereBelongsTo($actor);
        } elseif ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return $query->limit(60)->get();
    }
}
