<?php

namespace Tests\Feature;

use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DayDetailReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_fetch_their_own_day_detail(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->closed()->create([
            'user_id' => $employee->id,
        ]);
        PlanTask::factory()->done()->create([
            'daily_plan_id' => $plan->id,
            'title' => 'بازبینی کد',
        ]);

        $this->actingAs($employee)
            ->getJson(route('reports.days.show', $plan))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $plan->id)
            ->assertJsonPath('data.tasks.0.title', 'بازبینی کد');
    }

    public function test_employee_cannot_fetch_another_employees_day_detail(): void
    {
        $owner = User::factory()->employee()->create();
        $other = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->closed()->create([
            'user_id' => $owner->id,
        ]);

        $this->actingAs($other)
            ->getJson(route('reports.days.show', $plan))
            ->assertForbidden();
    }

    public function test_manager_can_fetch_an_employees_day_detail(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->closed()->create([
            'user_id' => $employee->id,
        ]);

        $this->actingAs($manager)
            ->getJson(route('reports.days.show', $plan))
            ->assertOk()
            ->assertJsonPath('data.user_id', $employee->id);
    }

    public function test_api_day_detail_requires_authorization(): void
    {
        $owner = User::factory()->employee()->create();
        $other = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create([
            'user_id' => $owner->id,
        ]);

        Sanctum::actingAs($other);

        $this->getJson("/api/v1/reports/day/{$plan->id}")
            ->assertForbidden();
    }
}
