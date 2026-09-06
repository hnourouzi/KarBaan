<?php

namespace App\Http\Requests\DailyPlan;

use App\DTOs\DailyPlan\CloseDailyPlanData;
use App\DTOs\DailyPlan\ExtraTaskData;
use App\DTOs\DailyPlan\TaskCloseoutData;
use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;
use App\Models\DailyPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CloseDailyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var DailyPlan $plan */
        $plan = $this->route('dailyPlan');

        return $this->user()?->can('close', $plan) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $extras = collect($this->input('extras', []))
            ->filter(fn ($extra) => filled(data_get($extra, 'title')))
            ->values()
            ->all();

        $this->merge([
            'extras' => $extras,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tasks' => ['required', 'array', 'min:1'],
            'tasks.*.status' => ['required', Rule::enum(TaskStatus::class)],
            'tasks.*.not_done_reason' => ['nullable', Rule::enum(NotDoneReason::class)],
            'tasks.*.not_done_note' => ['nullable', 'string', 'max:1000'],
            'extras' => ['nullable', 'array'],
            'extras.*.title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                /** @var DailyPlan $plan */
                $plan = $this->route('dailyPlan');
                $plannedIds = $plan->tasks()
                    ->where('is_extra', false)
                    ->pluck('id')
                    ->all();

                $payloadIds = collect($this->input('tasks', []))->keys()->map(fn ($id) => (int) $id)->all();

                foreach ($plannedIds as $id) {
                    if (! in_array($id, $payloadIds, true)) {
                        $validator->errors()->add("tasks.{$id}", 'وضعیت این وظیفه مشخص نشده است.');
                    }
                }

                foreach ($this->input('tasks', []) as $taskId => $task) {
                    $status = TaskStatus::tryFrom((string) ($task['status'] ?? ''));

                    if ($status === TaskStatus::NotDone && blank($task['not_done_reason'] ?? null)) {
                        $validator->errors()->add("tasks.{$taskId}.not_done_reason", 'برای وظیفه انجام‌نشده دلیل الزامی است.');
                    }

                    if (
                        $status === TaskStatus::NotDone
                        && ($task['not_done_reason'] ?? null) === NotDoneReason::Other->value
                        && blank($task['not_done_note'] ?? null)
                    ) {
                        $validator->errors()->add("tasks.{$taskId}.not_done_note", 'توضیح دلیل «سایر» الزامی است.');
                    }
                }
            },
        ];
    }

    public function toDto(): CloseDailyPlanData
    {
        $tasks = collect($this->validated('tasks'))
            ->map(function (array $task, int|string $id) {
                $status = TaskStatus::from($task['status']);

                return new TaskCloseoutData(
                    taskId: (int) $id,
                    status: $status,
                    notDoneReason: isset($task['not_done_reason'])
                        ? NotDoneReason::from($task['not_done_reason'])
                        : null,
                    notDoneNote: $task['not_done_note'] ?? null,
                );
            })
            ->values()
            ->all();

        $extras = collect($this->validated('extras') ?? [])
            ->map(fn (array $extra) => new ExtraTaskData($extra['title']))
            ->all();

        return new CloseDailyPlanData(
            tasks: $tasks,
            extras: $extras,
            notes: $this->filled('notes') ? $this->string('notes')->toString() : null,
        );
    }
}
