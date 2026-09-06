<?php

namespace App\Providers;

use App\Services\Contracts\AttendanceServiceInterface;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Contracts\DailyPlanServiceInterface;
use App\Services\Contracts\EmployeeServiceInterface;
use App\Services\Contracts\ReportServiceInterface;
use App\Services\Contracts\TaskCompletionServiceInterface;
use App\Services\Implementations\AttendanceService;
use App\Services\Implementations\AuthService;
use App\Services\Implementations\DailyPlanService;
use App\Services\Implementations\EmployeeService;
use App\Services\Implementations\ReportService;
use App\Services\Implementations\TaskCompletionService;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
        $this->app->bind(DailyPlanServiceInterface::class, DailyPlanService::class);
        $this->app->bind(TaskCompletionServiceInterface::class, TaskCompletionService::class);
        $this->app->bind(AttendanceServiceInterface::class, AttendanceService::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
    }
}
