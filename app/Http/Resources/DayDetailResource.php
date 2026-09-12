<?php

namespace App\Http\Resources;

use App\DTOs\Report\DayDetailData;
use App\Services\JalaliDateFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DayDetailData
 */
class DayDetailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var DayDetailData $detail */
        $detail = $this->resource;
        $jalali = app(JalaliDateFormatter::class);

        return [
            'id' => $detail->planId,
            'user_id' => $detail->userId,
            'user_name' => $detail->userName,
            'plan_date' => $detail->planDate->toDateString(),
            'plan_date_jalali' => $jalali->date($detail->planDate),
            'plan_date_full' => $jalali->full($detail->planDate),
            'day_name' => $jalali->dayName($detail->planDate),
            'status' => $detail->status->value,
            'status_label' => $detail->status->label(),
            'started_at' => $jalali->time($detail->startedAt),
            'closed_at' => $jalali->time($detail->closedAt),
            'hours_worked' => $detail->hoursWorked,
            'sessions' => WorkSessionResource::collection($detail->sessions)->resolve(),
            'can_assign_task' => $detail->canAssignTask,
            'assign_task_url' => route('daily-plans.manager-tasks.store', $detail->planId),
            'tasks' => PlanTaskResource::collection($detail->tasks)->resolve(),
        ];
    }
}
