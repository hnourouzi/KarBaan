<?php

namespace App\Services\Implementations;

use App\DTOs\DailyPlan\CloseDailyPlanData;
use App\DTOs\PlanTask\CreatePlanTaskData;
use App\DTOs\PlanTask\UpdatePlanTaskData;
use App\DTOs\PlanTask\UpdateTaskStatusData;
use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;
use App\Exceptions\CannotModifyClosedPlanException;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Services\Contracts\TaskCompletionServiceInterface;
use Illuminate\Validation\ValidationException;

class TaskCompletionService implements TaskCompletionServiceInterface
{
    public function add(DailyPlan $plan, CreatePlanTaskData $data): PlanTask
    {
        $this->ensurePlanIsOpen($plan);

        $status = $data->isExtra ? TaskStatus::Extra : TaskStatus::Planned;

        return $plan->tasks()->create([
            'title' => $data->title,
            'status' => $status,
            'is_extra' => $data->isExtra,
            'position' => $this->nextPosition($plan),
        ]);
    }

    public function update(PlanTask $task, UpdatePlanTaskData $data): PlanTask
    {
        $task->loadMissing('dailyPlan');

        $this->ensurePlanIsOpen($task->dailyPlan);

        $task->update([
            'title' => $data->title,
        ]);

        return $task->refresh();
    }

    public function updateStatus(PlanTask $task, UpdateTaskStatusData $data): PlanTask
    {
        $task->loadMissing('dailyPlan');

        $this->ensurePlanIsOpen($task->dailyPlan);
        $this->assertStatusPayload($data->status, $data->notDoneReason, $data->notDoneNote);

        $task->update([
            'status' => $data->status,
            'not_done_reason' => $data->status === TaskStatus::NotDone ? $data->notDoneReason : null,
            'not_done_note' => $this->noteFor($data->status, $data->notDoneReason, $data->notDoneNote),
        ]);

        return $task->refresh();
    }

    public function applyCloseout(DailyPlan $plan, CloseDailyPlanData $data): void
    {
        $this->ensurePlanIsOpen($plan);

        $tasks = $plan->tasks()->get()->keyBy('id');
        $plannedTasks = $tasks->where('is_extra', false);

        foreach ($plannedTasks as $task) {
            $closeout = collect($data->tasks)->first(
                fn ($item) => $item->taskId === $task->id,
            );

            if ($closeout === null) {
                throw ValidationException::withMessages([
                    "tasks.{$task->id}" => "وضعیت وظیفه «{$task->title}» مشخص نشده است.",
                ]);
            }

            $this->assertStatusPayload(
                $closeout->status,
                $closeout->notDoneReason,
                $closeout->notDoneNote,
            );

            $task->update([
                'status' => $closeout->status,
                'not_done_reason' => $closeout->status === TaskStatus::NotDone ? $closeout->notDoneReason : null,
                'not_done_note' => $this->noteFor(
                    $closeout->status,
                    $closeout->notDoneReason,
                    $closeout->notDoneNote,
                ),
            ]);
        }

        foreach ($data->extras as $extra) {
            $this->add($plan->refresh(), new CreatePlanTaskData($extra->title, true));
        }
    }

    private function ensurePlanIsOpen(DailyPlan $plan): void
    {
        if ($plan->isClosed()) {
            throw CannotModifyClosedPlanException::make();
        }
    }

    private function nextPosition(DailyPlan $plan): int
    {
        return (int) $plan->tasks()->max('position') + 1;
    }

    private function assertStatusPayload(
        TaskStatus $status,
        ?NotDoneReason $reason,
        ?string $note,
    ): void {
        if (! in_array($status, [TaskStatus::Done, TaskStatus::NotDone, TaskStatus::Extra], true)) {
            throw ValidationException::withMessages([
                'status' => 'وضعیت انتخاب‌شده برای بستن روز معتبر نیست.',
            ]);
        }

        if ($status === TaskStatus::NotDone && $reason === null) {
            throw ValidationException::withMessages([
                'not_done_reason' => 'برای وظیفه انجام‌نشده باید دلیل مشخص شود.',
            ]);
        }

        if ($status === TaskStatus::NotDone && $reason === NotDoneReason::Other && blank($note)) {
            throw ValidationException::withMessages([
                'not_done_note' => 'در صورت انتخاب «سایر» توضیح الزامی است.',
            ]);
        }
    }

    private function noteFor(TaskStatus $status, ?NotDoneReason $reason, ?string $note): ?string
    {
        if ($status !== TaskStatus::NotDone) {
            return null;
        }

        return $reason === NotDoneReason::Other ? $note : $note;
    }
}
