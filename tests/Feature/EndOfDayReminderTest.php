<?php

namespace Tests\Feature;

use App\Models\DailyPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EndOfDayReminderTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_employee_sees_reminder_after_configured_time_with_open_plan(): void
    {
        config(['karbaan.end_of_day_reminder_time' => '18:00']);
        Carbon::setTestNow(Carbon::today()->setTime(18, 30));

        $employee = User::factory()->employee()->create();
        DailyPlan::factory()->create([
            'user_id' => $employee->id,
            'plan_date' => now()->toDateString(),
        ]);

        $this->actingAs($employee)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('یادت نره امروزت رو ببندی')
            ->assertSee(route('daily-plans.close', DailyPlan::query()->first()), false);
    }

    public function test_reminder_is_hidden_before_configured_time(): void
    {
        config(['karbaan.end_of_day_reminder_time' => '18:00']);
        Carbon::setTestNow(Carbon::today()->setTime(17, 59));

        $employee = User::factory()->employee()->create();
        DailyPlan::factory()->create([
            'user_id' => $employee->id,
            'plan_date' => now()->toDateString(),
        ]);

        $this->actingAs($employee)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('یادت نره امروزت رو ببندی');
    }

    public function test_reminder_is_hidden_when_day_is_closed(): void
    {
        config(['karbaan.end_of_day_reminder_time' => '18:00']);
        Carbon::setTestNow(Carbon::today()->setTime(19, 0));

        $employee = User::factory()->employee()->create();
        DailyPlan::factory()->closed()->create([
            'user_id' => $employee->id,
            'plan_date' => now()->toDateString(),
        ]);

        $this->actingAs($employee)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('یادت نره امروزت رو ببندی');
    }

    public function test_reminder_is_hidden_when_employee_has_not_started_today(): void
    {
        config(['karbaan.end_of_day_reminder_time' => '18:00']);
        Carbon::setTestNow(Carbon::today()->setTime(19, 0));

        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('یادت نره امروزت رو ببندی');
    }

    public function test_manager_does_not_see_end_of_day_reminder(): void
    {
        config(['karbaan.end_of_day_reminder_time' => '18:00']);
        Carbon::setTestNow(Carbon::today()->setTime(19, 0));

        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('یادت نره امروزت رو ببندی');
    }
}
