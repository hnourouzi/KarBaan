<?php

namespace App\View\Components;

use App\Models\DailyPlan;
use App\Services\Contracts\DailyPlanServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class EndOfDayReminder extends Component
{
    public ?DailyPlan $plan = null;

    public function __construct(DailyPlanServiceInterface $plans)
    {
        $user = Auth::user();

        if ($user !== null) {
            $this->plan = $plans->openPlanNeedingEndOfDayReminder($user);
        }
    }

    public function render(): View
    {
        return view('components.end-of-day-reminder', [
            'plan' => $this->plan,
        ]);
    }
}
