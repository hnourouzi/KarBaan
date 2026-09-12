<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyPlan\CloseDailyPlanRequest;
use App\Http\Requests\DailyPlan\StoreDailyPlanRequest;
use App\Http\Resources\DailyPlanResource;
use App\Http\Responses\ApiResponse;
use App\Models\DailyPlan;
use App\Services\Contracts\DailyPlanServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyPlanController extends Controller
{
    public function __construct(private DailyPlanServiceInterface $plans) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', DailyPlan::class);

        $plans = $this->plans->listFor(
            $request->user(),
            $request->filled('user_id') ? $request->integer('user_id') : null,
        );

        return ApiResponse::success(DailyPlanResource::collection($plans));
    }

    public function store(StoreDailyPlanRequest $request): JsonResponse
    {
        $plan = $this->plans->open($request->user(), $request->toDto());

        return ApiResponse::success(
            DailyPlanResource::make($plan),
            'برنامه روزانه ثبت شد.',
            201,
        );
    }

    public function show(DailyPlan $dailyPlan): JsonResponse
    {
        $this->authorize('view', $dailyPlan);

        $dailyPlan->load(['tasks', 'user', 'workSessions']);

        return ApiResponse::success(DailyPlanResource::make($dailyPlan));
    }

    public function close(CloseDailyPlanRequest $request, DailyPlan $dailyPlan): JsonResponse
    {
        $plan = $this->plans->close($dailyPlan, $request->toDto());

        return ApiResponse::success(
            DailyPlanResource::make($plan),
            'روز کاری بسته شد.',
        );
    }
}
