<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\HistoryRequest;
use App\Services\Contracts\ReportServiceInterface;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __invoke(HistoryRequest $request, ReportServiceInterface $reports): View
    {
        return view('history.index', [
            'history' => $reports->history($request->toDto()),
            'filters' => [
                'period' => $request->validated('period'),
                'from' => $request->jalaliFrom(),
                'to' => $request->jalaliTo(),
            ],
        ]);
    }
}
