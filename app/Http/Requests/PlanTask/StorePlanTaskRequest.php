<?php

namespace App\Http\Requests\PlanTask;

use App\DTOs\PlanTask\CreatePlanTaskData;
use App\Models\DailyPlan;
use Illuminate\Foundation\Http\FormRequest;

class StorePlanTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var DailyPlan $plan */
        $plan = $this->route('dailyPlan');

        return $this->user()?->can('update', $plan) ?? false;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'is_extra' => ['sometimes', 'boolean'],
        ];
    }

    public function toDto(): CreatePlanTaskData
    {
        return new CreatePlanTaskData(
            title: $this->string('title')->toString(),
            isExtra: $this->boolean('is_extra'),
        );
    }
}
