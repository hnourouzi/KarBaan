<?php

namespace Database\Factories;

use App\Models\DailyPlan;
use App\Models\WorkSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkSession>
 */
class WorkSessionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'daily_plan_id' => DailyPlan::factory(),
            'started_at' => now()->subHours(2),
            'ended_at' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'started_at' => now()->subHours(4),
            'ended_at' => now()->subHours(1),
        ]);
    }
}
