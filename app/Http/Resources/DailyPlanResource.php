<?php

namespace App\Http\Resources;

use App\Models\DailyPlan;
use App\Services\JalaliDateFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DailyPlan
 */
class DailyPlanResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'plan_date' => $this->plan_date->toDateString(),
            'plan_date_jalali' => app(JalaliDateFormatter::class)->date($this->plan_date),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'started_at' => $this->started_at?->toIso8601String(),
            'closed_at' => $this->closed_at?->toIso8601String(),
            'hours_worked' => $this->hours_worked,
            'notes' => $this->notes,
            'user' => UserResource::make($this->whenLoaded('user')),
            'tasks' => PlanTaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}
