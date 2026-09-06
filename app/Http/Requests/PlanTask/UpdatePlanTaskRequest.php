<?php

namespace App\Http\Requests\PlanTask;

use App\DTOs\PlanTask\UpdatePlanTaskData;
use App\Models\PlanTask;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var PlanTask $task */
        $task = $this->route('planTask');

        return $this->user()?->can('update', $task) ?? false;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
        ];
    }

    public function toDto(): UpdatePlanTaskData
    {
        return new UpdatePlanTaskData(
            title: $this->string('title')->toString(),
        );
    }
}
