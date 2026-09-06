<?php

namespace App\Http\Requests\PlanTask;

use App\DTOs\PlanTask\UpdateTaskStatusData;
use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;
use App\Models\PlanTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTaskStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var PlanTask $task */
        $task = $this->route('planTask');

        return $this->user()?->can('update', $task) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'not_done_reason' => ['nullable', Rule::enum(NotDoneReason::class)],
            'not_done_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $status = TaskStatus::from($this->string('status')->toString());

                if ($status === TaskStatus::NotDone && blank($this->input('not_done_reason'))) {
                    $validator->errors()->add('not_done_reason', 'برای وظیفه انجام‌نشده دلیل الزامی است.');
                }

                if (
                    $status === TaskStatus::NotDone
                    && $this->input('not_done_reason') === NotDoneReason::Other->value
                    && blank($this->input('not_done_note'))
                ) {
                    $validator->errors()->add('not_done_note', 'توضیح دلیل «سایر» الزامی است.');
                }
            },
        ];
    }

    public function toDto(): UpdateTaskStatusData
    {
        $status = TaskStatus::from($this->string('status')->toString());

        return new UpdateTaskStatusData(
            status: $status,
            notDoneReason: $this->filled('not_done_reason')
                ? NotDoneReason::from($this->string('not_done_reason')->toString())
                : null,
            notDoneNote: $this->filled('not_done_note') ? $this->string('not_done_note')->toString() : null,
        );
    }
}
