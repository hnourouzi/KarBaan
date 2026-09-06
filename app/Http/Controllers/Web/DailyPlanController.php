<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyPlan\CloseDailyPlanRequest;
use App\Http\Requests\DailyPlan\StoreDailyPlanRequest;
use App\Models\DailyPlan;
use App\Services\Contracts\DailyPlanServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyPlanController extends Controller
{
    public function __construct(private DailyPlanServiceInterface $plans) {}

    public function create(): View
    {
        $this->authorize('create', DailyPlan::class);

        return view('daily-plans.create');
    }

    public function store(StoreDailyPlanRequest $request): RedirectResponse
    {
        $this->plans->open($request->user(), $request->toDto());

        return redirect()
            ->route('dashboard')
            ->with('success', 'برنامه امروز ثبت شد.');
    }

    public function show(Request $request, DailyPlan $dailyPlan): View
    {
        $this->authorize('view', $dailyPlan);

        $dailyPlan->load(['tasks', 'user']);

        return view('daily-plans.show', [
            'plan' => $dailyPlan,
        ]);
    }

    public function closeForm(DailyPlan $dailyPlan): View
    {
        $this->authorize('close', $dailyPlan);

        $dailyPlan->load(['tasks', 'user']);

        return view('daily-plans.close', [
            'plan' => $dailyPlan,
        ]);
    }

    public function close(CloseDailyPlanRequest $request, DailyPlan $dailyPlan): RedirectResponse
    {
        $this->plans->close($dailyPlan, $request->toDto());

        return redirect()
            ->route('dashboard')
            ->with('success', 'روز کاری بسته شد.');
    }
}
