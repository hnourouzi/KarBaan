<?php

namespace Database\Factories;

use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlanTask>
 */
class PlanTaskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'daily_plan_id' => DailyPlan::factory(),
            'title' => fake()->sentence(4),
            'status' => TaskStatus::Planned,
            'is_extra' => false,
            'not_done_reason' => null,
            'not_done_note' => null,
            'position' => fake()->numberBetween(1, 8),
        ];
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Done,
        ]);
    }

    public function notDone(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::NotDone,
            'not_done_reason' => NotDoneReason::TimeShortage,
        ]);
    }

    public function extra(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Extra,
            'is_extra' => true,
        ]);
    }
}
