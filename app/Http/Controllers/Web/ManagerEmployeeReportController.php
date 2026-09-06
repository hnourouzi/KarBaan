<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ManagerEmployeeReportRequest;
use App\Models\User;
use App\Services\Contracts\ReportServiceInterface;
use Illuminate\View\View;

class ManagerEmployeeReportController extends Controller
{
    public function __invoke(ManagerEmployeeReportRequest $request, ReportServiceInterface $reports): View
    {
        $history = $request->filled('user_id')
            ? $reports->history($request->toDto())
            : null;

        return view('reports.employee', [
            'history' => $history,
            'employees' => User::query()->orderBy('name')->orderBy('id')->get(),
            'filters' => [
                'period' => $request->validated('period'),
                'from' => $request->jalaliFrom(),
                'to' => $request->jalaliTo(),
                'user_id' => $request->validated('user_id'),
            ],
        ]);
    }
}
