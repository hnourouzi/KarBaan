<?php

namespace App\Services\Contracts;

use App\DTOs\Report\DayDetailData;
use App\DTOs\Report\PeriodHistoryData;
use App\DTOs\Report\ReportQueryData;
use App\DTOs\Report\ReportResultData;
use App\Models\DailyPlan;

interface ReportServiceInterface
{
    public function generate(ReportQueryData $query): ReportResultData;

    public function history(ReportQueryData $query): PeriodHistoryData;

    public function dayDetail(DailyPlan $plan): DayDetailData;
}
