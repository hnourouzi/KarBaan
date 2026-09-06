<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportRequest;
use App\Models\User;
use App\Services\Contracts\ReportServiceInterface;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __invoke(ReportRequest $request, ReportServiceInterface $reports): View
    {
        return view('reports.index', [
            'report' => $reports->generate($request->toDto()),
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
