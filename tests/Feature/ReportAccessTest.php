<?php

namespace Tests\Feature;

use App\Enums\ReportPeriod;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_periodic_reports(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->closed()->create([
            'user_id' => $employee->id,
            'plan_date' => now()->toDateString(),
        ]);
        PlanTask::factory()->done()->create([
            'daily_plan_id' => $plan->id,
        ]);

        $this->actingAs($manager)
            ->get(route('reports.index', [
                'period' => ReportPeriod::Weekly->value,
            ]))
            ->assertOk()
            ->assertSee($employee->name);
    }

    public function test_employee_cannot_view_team_reports(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get(route('reports.index'))
            ->assertForbidden();
    }
}
