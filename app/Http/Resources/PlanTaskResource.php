<?php

namespace App\Http\Resources;

use App\Models\PlanTask;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PlanTask
 */
class PlanTaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'is_extra' => $this->is_extra,
            'not_done_reason' => $this->not_done_reason?->value,
            'not_done_reason_label' => $this->not_done_reason?->label(),
            'not_done_note' => $this->not_done_note,
            'position' => $this->position,
        ];
    }
}
