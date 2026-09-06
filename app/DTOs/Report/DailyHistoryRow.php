<?php

namespace App\DTOs\Report;

use App\Enums\DayHistoryStatus;
use Illuminate\Support\Carbon;

readonly class DailyHistoryRow
{
    public function __construct(
        public Carbon $date,
        public ?int $planId,
        public DayHistoryStatus $status,
        public float $hoursWorked,
        public int $plannedCount,
        public int $doneCount,
        public int $notDoneCount,
        public int $extraCount,
    ) {}
}
