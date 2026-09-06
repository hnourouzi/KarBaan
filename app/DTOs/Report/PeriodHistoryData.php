<?php

namespace App\DTOs\Report;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

readonly class PeriodHistoryData
{
    /**
     * @param  Collection<int, DailyHistoryRow>  $days
     */
    public function __construct(
        public Carbon $from,
        public Carbon $to,
        public EmployeeReportSummary $summary,
        public Collection $days,
    ) {}
}
