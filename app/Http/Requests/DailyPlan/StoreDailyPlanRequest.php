<?php

namespace App\Http\Requests\DailyPlan;

use App\DTOs\DailyPlan\CreateDailyPlanData;
use App\Models\DailyPlan;
use Illuminate\Foundation\Http\FormRequest;

class StoreDailyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', DailyPlan::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'plan_date' => ['nullable', 'date'],
            'titles' => ['required', 'array', 'min:1'],
            'titles.*' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function toDto(): CreateDailyPlanData
    {
        $titles = collect($this->validated('titles'))
            ->map(fn (string $title) => trim($title))
            ->filter()
            ->values()
            ->all();

        return new CreateDailyPlanData(
            planDate: $this->date('plan_date') ?? now()->startOfDay(),
            taskTitles: $titles,
            notes: $this->filled('notes') ? $this->string('notes')->toString() : null,
        );
    }
}
