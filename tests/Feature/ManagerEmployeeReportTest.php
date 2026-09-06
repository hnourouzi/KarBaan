<?php

namespace Tests\Feature;

use App\Enums\ReportPeriod;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerEmployeeReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_an_employees_periodic_report(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create(['name' => 'سارا احمدی']);
        $plan = DailyPlan::factory()->closed()->create([
            'user_id' => $employee->id,
            'plan_date' => now()->toDateString(),
            'hours_worked' => 8,
        ]);
        PlanTask::factory()->done()->count(2)->create([
            'daily_plan_id' => $plan->id,
        ]);

        $this->actingAs($manager)
            ->get(route('reports.employee', [
                'period' => ReportPeriod::Weekly->value,
                'user_id' => $employee->id,
            ]))
            ->assertOk()
            ->assertSee('سارا احمدی')
            ->assertSee('ساعات کار انجام‌شده');
    }

    public function test_employee_cannot_open_the_manager_report_page(): void
    {
        $employee = User::factory()->employee()->create();
        $other = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get(route('reports.employee', [
                'user_id' => $other->id,
            ]))
            ->assertForbidden();
    }

    public function test_manager_report_requires_a_valid_employee(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('reports.employee', [
                'period' => ReportPeriod::Weekly->value,
                'user_id' => 99999,
            ]))
            ->assertSessionHasErrors('user_id');
    }
}
