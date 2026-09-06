<?php

namespace App\Services\Implementations;

use App\DTOs\Report\DailyHistoryRow;
use App\DTOs\Report\DayDetailData;
use App\DTOs\Report\EmployeeReportSummary;
use App\DTOs\Report\PeriodHistoryData;
use App\DTOs\Report\ReasonBreakdown;
use App\DTOs\Report\ReportQueryData;
use App\DTOs\Report\ReportResultData;
use App\Enums\DailyPlanStatus;
use App\Enums\DayHistoryStatus;
use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use App\Services\Contracts\ReportServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ReportService implements ReportServiceInterface
{
    public function generate(ReportQueryData $query): ReportResultData
    {
        $plans = $this->plansInRange($query);

        $employees = $plans
            ->groupBy('user_id')
            ->map(fn (Collection $userPlans) => $this->summarizeFromPlans($userPlans->first()->user, $userPlans))
            ->values();

        $allTasks = $plans->flatMap->tasks;
        $counts = $this->taskCounts($allTasks);

        return new ReportResultData(
            from: $query->from,
            to: $query->to,
            employees: $employees,
            plannedCount: $counts['planned'],
            doneCount: $counts['done'],
            notDoneCount: $counts['notDone'],
            extraCount: $counts['extra'],
            completionRate: $this->rate($counts['done'], $counts['planned']),
            hoursWorked: round((float) $plans->sum('hours_worked'), 2),
            reasonTotals: $this->reasonBreakdown($allTasks->where('is_extra', false)),
        );
    }

    public function history(ReportQueryData $query): PeriodHistoryData
    {
        if ($query->userId === null) {
            throw new InvalidArgumentException('ساخت تاریخچه نیازمند شناسه کاربر است.');
        }

        $user = User::query()->findOrFail($query->userId);
        $plans = $this->plansInRange($query)->keyBy(
            fn (DailyPlan $plan) => $plan->plan_date->toDateString(),
        );

        $days = collect();

        for ($date = $query->from->copy()->startOfDay(); $date->lte($query->to); $date->addDay()) {
            $days->push($this->rowForDay($date->copy(), $plans->get($date->toDateString())));
        }

        return new PeriodHistoryData(
            from: $query->from,
            to: $query->to,
            summary: $this->summarizeFromRows($user, $days, $plans->values()),
            days: $days,
        );
    }

    public function dayDetail(DailyPlan $plan): DayDetailData
    {
        $plan->loadMissing(['user', 'tasks']);

        return new DayDetailData(
            planId: $plan->id,
            userId: $plan->user_id,
            userName: $plan->user->name,
            planDate: $plan->plan_date,
            status: $plan->status,
            startedAt: $plan->started_at,
            closedAt: $plan->closed_at,
            hoursWorked: $plan->hours_worked !== null ? (float) $plan->hours_worked : null,
            tasks: $plan->tasks,
        );
    }

    /**
     * @return Collection<int, DailyPlan>
     */
    private function plansInRange(ReportQueryData $query): Collection
    {
        return DailyPlan::query()
            ->with(['user', 'tasks'])
            ->whereDate('plan_date', '>=', $query->from)
            ->whereDate('plan_date', '<=', $query->to)
            ->when($query->userId !== null, fn ($builder) => $builder->where('user_id', $query->userId))
            ->orderBy('user_id')
            ->orderBy('plan_date')
            ->get();
    }

    /**
     * @param  Collection<int, DailyPlan>  $plans
     */
    private function summarizeFromPlans(User $user, Collection $plans): EmployeeReportSummary
    {
        $tasks = $plans->flatMap->tasks;
        $counts = $this->taskCounts($tasks);

        return new EmployeeReportSummary(
            userId: $user->id,
            userName: $user->name,
            plannedCount: $counts['planned'],
            doneCount: $counts['done'],
            notDoneCount: $counts['notDone'],
            extraCount: $counts['extra'],
            completionRate: $this->rate($counts['done'], $counts['planned']),
            hoursWorked: round((float) $plans->sum('hours_worked'), 2),
            closedDays: $plans->where('status', DailyPlanStatus::Closed)->count(),
            reasons: $this->reasonBreakdown($tasks->where('is_extra', false)),
        );
    }

    /**
     * @param  Collection<int, DailyHistoryRow>  $days
     * @param  Collection<int, DailyPlan>  $plans
     */
    private function summarizeFromRows(User $user, Collection $days, Collection $plans): EmployeeReportSummary
    {
        $planned = (int) $days->sum('plannedCount');
        $done = (int) $days->sum('doneCount');

        return new EmployeeReportSummary(
            userId: $user->id,
            userName: $user->name,
            plannedCount: $planned,
            doneCount: $done,
            notDoneCount: (int) $days->sum('notDoneCount'),
            extraCount: (int) $days->sum('extraCount'),
            completionRate: $this->rate($done, $planned),
            hoursWorked: round((float) $days->sum('hoursWorked'), 2),
            closedDays: $days->where('status', DayHistoryStatus::Closed)->count(),
            reasons: $this->reasonBreakdown($plans->flatMap->tasks->where('is_extra', false)),
        );
    }

    private function rowForDay(Carbon $date, ?DailyPlan $plan): DailyHistoryRow
    {
        if ($plan === null) {
            return new DailyHistoryRow(
                date: $date,
                planId: null,
                status: DayHistoryStatus::None,
                hoursWorked: 0.0,
                plannedCount: 0,
                doneCount: 0,
                notDoneCount: 0,
                extraCount: 0,
            );
        }

        $counts = $this->taskCounts($plan->tasks);

        return new DailyHistoryRow(
            date: $date,
            planId: $plan->id,
            status: $plan->isClosed() ? DayHistoryStatus::Closed : DayHistoryStatus::Open,
            hoursWorked: round((float) ($plan->hours_worked ?? 0), 2),
            plannedCount: $counts['planned'],
            doneCount: $counts['done'],
            notDoneCount: $counts['notDone'],
            extraCount: $counts['extra'],
        );
    }

    /**
     * @param  Collection<int, PlanTask>  $tasks
     * @return array{planned: int, done: int, notDone: int, extra: int}
     */
    private function taskCounts(Collection $tasks): array
    {
        $planned = $tasks->where('is_extra', false);

        return [
            'planned' => $planned->count(),
            'done' => $planned->where('status', TaskStatus::Done)->count(),
            'notDone' => $planned->where('status', TaskStatus::NotDone)->count(),
            'extra' => $tasks->where('is_extra', true)->count(),
        ];
    }

    /**
     * @param  Collection<int, PlanTask>  $planned
     * @return list<ReasonBreakdown>
     */
    private function reasonBreakdown(Collection $planned): array
    {
        return $planned
            ->where('status', TaskStatus::NotDone)
            ->whereNotNull('not_done_reason')
            ->groupBy(fn (PlanTask $task) => $task->not_done_reason->value)
            ->map(function (Collection $group) {
                /** @var NotDoneReason $reason */
                $reason = $group->first()->not_done_reason;

                return new ReasonBreakdown($reason, $group->count());
            })
            ->sortByDesc(fn (ReasonBreakdown $item) => $item->count)
            ->values()
            ->all();
    }

    private function rate(int $done, int $planned): float
    {
        if ($planned === 0) {
            return 0.0;
        }

        return round(($done / $planned) * 100, 1);
    }
}
