<?php

namespace App\DTOs\Report;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

readonly class ReportResultData
{
    /**
     * @param  Collection<int, EmployeeReportSummary>  $employees
     * @param  list<ReasonBreakdown>  $reasonTotals
     */
    public function __construct(
        public Carbon $from,
        public Carbon $to,
        public Collection $employees,
        public int $plannedCount,
        public int $doneCount,
        public int $notDoneCount,
        public int $extraCount,
        public float $completionRate,
        public float $hoursWorked,
        public array $reasonTotals,
    ) {}
}
