<?php

namespace App\DTOs\Report;

use App\Enums\DailyPlanStatus;
use App\Models\PlanTask;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

readonly class DayDetailData
{
    /**
     * @param  Collection<int, PlanTask>  $tasks
     */
    public function __construct(
        public int $planId,
        public int $userId,
        public string $userName,
        public Carbon $planDate,
        public DailyPlanStatus $status,
        public ?Carbon $startedAt,
        public ?Carbon $closedAt,
        public ?float $hoursWorked,
        public Collection $tasks,
    ) {}
}
