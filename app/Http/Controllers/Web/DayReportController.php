<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\DayDetailResource;
use App\Http\Responses\ApiResponse;
use App\Models\DailyPlan;
use App\Services\Contracts\ReportServiceInterface;
use Illuminate\Http\JsonResponse;

class DayReportController extends Controller
{
    public function __invoke(DailyPlan $dailyPlan, ReportServiceInterface $reports): JsonResponse
    {
        $this->authorize('view', $dailyPlan);

        return ApiResponse::success(
            new DayDetailResource($reports->dayDetail($dailyPlan)),
        );
    }
}
