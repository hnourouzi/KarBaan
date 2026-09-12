<?php

namespace App\Models;

use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;
use Database\Factories\PlanTaskFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'daily_plan_id',
    'assigned_by',
    'assigned_note',
    'title',
    'status',
    'is_extra',
    'not_done_reason',
    'not_done_note',
    'position',
])]
class PlanTask extends Model
{
    /** @use HasFactory<PlanTaskFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
            'is_extra' => 'boolean',
            'not_done_reason' => NotDoneReason::class,
        ];
    }

    /**
     * @return BelongsTo<DailyPlan, $this>
     */
    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isAssignedByManager(): bool
    {
        return $this->assigned_by !== null;
    }

    public function isPlanned(): bool
    {
        return $this->status === TaskStatus::Planned;
    }

    public function isDone(): bool
    {
        return $this->status === TaskStatus::Done;
    }

    public function isNotDone(): bool
    {
        return $this->status === TaskStatus::NotDone;
    }
}
