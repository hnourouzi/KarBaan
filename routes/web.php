<?php

use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\LogoutController;
use App\Http\Controllers\Web\DailyPlanController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DayReportController;
use App\Http\Controllers\Web\EmployeeController;
use App\Http\Controllers\Web\HistoryController;
use App\Http\Controllers\Web\ManagerEmployeeReportController;
use App\Http\Controllers\Web\PlanTaskController;
use App\Http\Controllers\Web\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/daily-plans/create', [DailyPlanController::class, 'create'])->name('daily-plans.create');
    Route::post('/daily-plans', [DailyPlanController::class, 'store'])->name('daily-plans.store');
    Route::get('/daily-plans/{dailyPlan}', [DailyPlanController::class, 'show'])->name('daily-plans.show');
    Route::get('/daily-plans/{dailyPlan}/close', [DailyPlanController::class, 'closeForm'])->name('daily-plans.close');
    Route::post('/daily-plans/{dailyPlan}/close', [DailyPlanController::class, 'close'])->name('daily-plans.close.store');
    Route::post('/daily-plans/{dailyPlan}/tasks', [PlanTaskController::class, 'store'])->name('daily-plans.tasks.store');
    Route::patch('/plan-tasks/{planTask}/status', [PlanTaskController::class, 'updateStatus'])->name('plan-tasks.status');
    Route::get('/history', HistoryController::class)->name('history.index');
    Route::get('/reports/days/{dailyPlan}', DayReportController::class)->name('reports.days.show');

    Route::middleware('role:manager,admin')->group(function () {
        Route::get('/reports', ReportController::class)->name('reports.index');
        Route::get('/reports/employee', ManagerEmployeeReportController::class)->name('reports.employee');
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });
});
