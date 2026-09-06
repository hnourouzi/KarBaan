<?php

namespace App\Http\Controllers\Web;

use App\DTOs\Report\ReportQueryData;
use App\Enums\ReportPeriod;
use App\Http\Controllers\Controller;
use App\Services\Contracts\DailyPlanServiceInterface;
use App\Services\Contracts\ReportServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(
        Request $request,
        DailyPlanServiceInterface $plans,
        ReportServiceInterface $reports,
    ): View {
        $user = $request->user();

        if ($user->canManageTeam()) {
            $report = $reports->generate(new ReportQueryData(
                period: ReportPeriod::Weekly,
                from: now()->startOfWeek(),
                to: now()->endOfWeek(),
            ));

            return view('dashboard.manager', [
                'report' => $report,
                'todayAttendance' => $reports->getTodayAttendanceOverview(),
            ]);
        }

        return view('dashboard.employee', [
            'plan' => $plans->todayFor($user),
        ]);
    }
}
