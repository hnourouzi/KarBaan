<?php

namespace App\Http\Requests\WorkSession;

use App\DTOs\WorkSession\StartWorkSessionData;
use App\Models\DailyPlan;
use App\Services\Contracts\DailyPlanServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

class StartWorkSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $plan = $this->plan();

        return $plan !== null && ($this->user()?->can('update', $plan) ?? false);
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'daily_plan_id' => ['nullable', 'integer', 'exists:daily_plans,id'],
        ];
    }

    public function toDto(): StartWorkSessionData
    {
        $plan = $this->plan();

        if ($plan === null) {
            abort(403);
        }

        return new StartWorkSessionData($plan);
    }

    public function plan(): ?DailyPlan
    {
        if ($this->route('dailyPlan') instanceof DailyPlan) {
            return $this->route('dailyPlan');
        }

        if ($this->filled('daily_plan_id')) {
            return DailyPlan::query()->find($this->integer('daily_plan_id'));
        }

        $user = $this->user();

        if ($user === null) {
            return null;
        }

        return $this->container->make(DailyPlanServiceInterface::class)->todayFor($user);
    }
}
