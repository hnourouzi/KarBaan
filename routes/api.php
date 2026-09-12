<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DailyPlanController;
use App\Http\Controllers\Api\V1\DayReportController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\ManagerPlanTaskController;
use App\Http\Controllers\Api\V1\PlanTaskController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\WorkSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::get('/daily-plans', [DailyPlanController::class, 'index']);
        Route::post('/daily-plans', [DailyPlanController::class, 'store']);
        Route::get('/daily-plans/{dailyPlan}', [DailyPlanController::class, 'show']);
        Route::post('/daily-plans/{dailyPlan}/close', [DailyPlanController::class, 'close']);
        Route::post('/daily-plans/{dailyPlan}/tasks', [PlanTaskController::class, 'store']);
        Route::post('/daily-plans/{dailyPlan}/manager-tasks', [ManagerPlanTaskController::class, 'store']);
        Route::post('/work-sessions/start', [WorkSessionController::class, 'store']);
        Route::post('/work-sessions/{workSession}/end', [WorkSessionController::class, 'end']);
        Route::patch('/plan-tasks/{planTask}', [PlanTaskController::class, 'update']);
        Route::patch('/plan-tasks/{planTask}/status', [PlanTaskController::class, 'updateStatus']);

        Route::get('/reports', ReportController::class);
        Route::get('/reports/day/{dailyPlan}', DayReportController::class);

        Route::apiResource('employees', EmployeeController::class)->names('api.employees');
    });
});
