<?php

namespace Database\Factories;

use App\Enums\DailyPlanStatus;
use App\Models\DailyPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyPlan>
 */
class DailyPlanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = now()->subHours(8);

        return [
            'user_id' => User::factory(),
            'plan_date' => now()->toDateString(),
            'status' => DailyPlanStatus::Open,
            'started_at' => $startedAt,
            'closed_at' => null,
            'hours_worked' => null,
            'notes' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(function (array $attributes) {
            $startedAt = now()->subHours(8);
            $closedAt = now()->subHour();

            return [
                'status' => DailyPlanStatus::Closed,
                'started_at' => $startedAt,
                'closed_at' => $closedAt,
                'hours_worked' => 7.00,
            ];
        });
    }

    public function configure(): static
    {
        return $this->afterCreating(function (DailyPlan $plan) {
            if ($plan->workSessions()->exists()) {
                return;
            }

            $plan->workSessions()->create([
                'started_at' => $plan->started_at,
                'ended_at' => $plan->isClosed() ? $plan->closed_at : null,
            ]);
        });
    }
}
