<?php

namespace App\Models;

use Database\Factories\WorkSessionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'daily_plan_id',
    'started_at',
    'ended_at',
])]
class WorkSession extends Model
{
    /** @use HasFactory<WorkSessionFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<DailyPlan, $this>
     */
    public function dailyPlan(): BelongsTo
    {
        return $this->belongsTo(DailyPlan::class);
    }

    public function isOpen(): bool
    {
        return $this->ended_at === null;
    }

    public function isClosed(): bool
    {
        return $this->ended_at !== null;
    }

    public function durationHours(?Carbon $until = null): float
    {
        $end = $this->ended_at ?? $until ?? now();

        if ($end->lt($this->started_at)) {
            return 0.0;
        }

        return round($this->started_at->diffInMinutes($end) / 60, 2);
    }
}
