<?php

namespace App\Models;

use App\Enums\DailyPlanStatus;
use Database\Factories\DailyPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'plan_date',
    'status',
    'started_at',
    'closed_at',
    'hours_worked',
    'notes',
])]
class DailyPlan extends Model
{
    /** @use HasFactory<DailyPlanFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
            'status' => DailyPlanStatus::class,
            'started_at' => 'datetime',
            'closed_at' => 'datetime',
            'hours_worked' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<PlanTask, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(PlanTask::class)->orderBy('position')->orderBy('id');
    }

    public function isOpen(): bool
    {
        return $this->status === DailyPlanStatus::Open;
    }

    public function isClosed(): bool
    {
        return $this->status === DailyPlanStatus::Closed;
    }

    #[Scope]
    protected function open(Builder $query): Builder
    {
        return $query->where('status', DailyPlanStatus::Open);
    }

    #[Scope]
    protected function closed(Builder $query): Builder
    {
        return $query->where('status', DailyPlanStatus::Closed);
    }

    #[Scope]
    protected function forDate(Builder $query, \DateTimeInterface|string $date): Builder
    {
        return $query->whereDate('plan_date', $date);
    }
}
