<?php

namespace App\DTOs\Report;

readonly class EmployeeReportSummary
{
    /**
     * @param  list<ReasonBreakdown>  $reasons
     */
    public function __construct(
        public int $userId,
        public string $userName,
        public int $plannedCount,
        public int $doneCount,
        public int $notDoneCount,
        public int $extraCount,
        public float $completionRate,
        public float $hoursWorked,
        public int $closedDays,
        public array $reasons,
    ) {}
}
