<?php

namespace App\Services\Contracts;

use App\DTOs\Report\DayDetailData;
use App\DTOs\Report\PeriodHistoryData;
use App\DTOs\Report\ReportQueryData;
use App\DTOs\Report\ReportResultData;
use App\DTOs\Report\TodayAttendanceDTO;
use App\Models\DailyPlan;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

interface ReportServiceInterface
{
    public function generate(ReportQueryData $query): ReportResultData;

    public function history(ReportQueryData $query): PeriodHistoryData;

    public function dayDetail(DailyPlan $plan, ?User $actor = null): DayDetailData;

    /**
     * @return Collection<int, TodayAttendanceDTO>
     */
    public function getTodayAttendanceOverview(?CarbonInterface $date = null): Collection;
}
