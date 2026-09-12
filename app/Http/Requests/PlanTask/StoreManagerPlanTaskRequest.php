<?php

namespace App\Http\Requests\PlanTask;

use App\DTOs\PlanTask\ManagerTaskData;
use App\Models\DailyPlan;
use Illuminate\Foundation\Http\FormRequest;

class StoreManagerPlanTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var DailyPlan $plan */
        $plan = $this->route('dailyPlan');

        return $this->user()?->can('assignTask', $plan) ?? false;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function toDto(): ManagerTaskData
    {
        return new ManagerTaskData(
            title: $this->string('title')->toString(),
            note: $this->filled('note') ? $this->string('note')->toString() : null,
        );
    }
}
