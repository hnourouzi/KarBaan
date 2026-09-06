<?php

namespace Tests\Feature;

use App\Enums\ReportPeriod;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use App\Services\JalaliDateFormatter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_view_their_own_history(): void
    {
        $employee = User::factory()->employee()->create();
        $date = now()->subDays(2)->startOfDay();
        $plan = DailyPlan::factory()->closed()->create([
            'user_id' => $employee->id,
            'plan_date' => $date->toDateString(),
        ]);
        PlanTask::factory()->done()->create([
            'daily_plan_id' => $plan->id,
        ]);

        $this->actingAs($employee)
            ->get(route('history.index', [
                'period' => ReportPeriod::Monthly->value,
            ]))
            ->assertOk()
            ->assertSee('سوابق کاری من')
            ->assertSee((new JalaliDateFormatter)->date($date));
    }

    public function test_history_ignores_another_employees_id_in_the_query_string(): void
    {
        $employee = User::factory()->employee()->create(['name' => 'کارمند اصلی']);
        $other = User::factory()->employee()->create(['name' => 'کارمند دیگر']);
        DailyPlan::factory()->closed()->create([
            'user_id' => $other->id,
            'plan_date' => now()->subDay()->toDateString(),
        ]);

        $this->actingAs($employee)
            ->get(route('history.index', [
                'period' => ReportPeriod::Weekly->value,
                'user_id' => $other->id,
            ]))
            ->assertOk()
            ->assertDontSee('کارمند دیگر');
    }

    public function test_employee_can_filter_history_with_a_jalali_custom_range(): void
    {
        $employee = User::factory()->employee()->create();
        $date = now()->subDays(4)->startOfDay();
        DailyPlan::factory()->closed()->create([
            'user_id' => $employee->id,
            'plan_date' => $date->toDateString(),
            'hours_worked' => 7.5,
        ]);

        $jalali = (new JalaliDateFormatter)->date($date);

        $this->actingAs($employee)
            ->get(route('history.index', [
                'period' => ReportPeriod::Custom->value,
                'from' => $jalali,
                'to' => $jalali,
            ]))
            ->assertOk()
            ->assertSee('7.5');
    }

    public function test_guest_is_redirected_from_history(): void
    {
        $this->get(route('history.index'))->assertRedirect(route('login'));
    }
}
