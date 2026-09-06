<?php

namespace Tests\Feature;

use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyPlanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_open_todays_plan(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->post(route('daily-plans.store'), [
                'titles' => ['بررسی ایمیل', 'جلسه تیم'],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('daily_plans', [
            'user_id' => $employee->id,
            'plan_date' => now()->toDateString(),
            'status' => 'open',
        ]);

        $this->assertDatabaseCount('plan_tasks', 2);
    }

    public function test_employee_cannot_open_two_plans_for_the_same_day(): void
    {
        $employee = User::factory()->employee()->create();

        DailyPlan::factory()->create([
            'user_id' => $employee->id,
            'plan_date' => now()->toDateString(),
        ]);

        $this->actingAs($employee)
            ->from(route('daily-plans.create'))
            ->post(route('daily-plans.store'), [
                'titles' => ['کار جدید'],
            ])
            ->assertRedirect(route('daily-plans.create'))
            ->assertSessionHas('error');
    }

    public function test_employee_can_add_an_extra_task_to_an_open_plan(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create([
            'user_id' => $employee->id,
        ]);

        $this->actingAs($employee)
            ->post(route('daily-plans.tasks.store', $plan), [
                'title' => 'کار فوری',
                'is_extra' => true,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('plan_tasks', [
            'daily_plan_id' => $plan->id,
            'title' => 'کار فوری',
            'is_extra' => true,
            'status' => TaskStatus::Extra->value,
        ]);
    }

    public function test_employee_can_close_the_day_with_reasons_for_incomplete_tasks(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create([
            'user_id' => $employee->id,
        ]);
        $done = PlanTask::factory()->create([
            'daily_plan_id' => $plan->id,
            'title' => 'کار اول',
            'position' => 1,
        ]);
        $notDone = PlanTask::factory()->create([
            'daily_plan_id' => $plan->id,
            'title' => 'کار دوم',
            'position' => 2,
        ]);

        $this->actingAs($employee)
            ->post(route('daily-plans.close.store', $plan), [
                'tasks' => [
                    $done->id => ['status' => TaskStatus::Done->value],
                    $notDone->id => [
                        'status' => TaskStatus::NotDone->value,
                        'not_done_reason' => NotDoneReason::TimeShortage->value,
                    ],
                ],
                'extras' => [
                    ['title' => 'پشتیبانی تلفنی'],
                ],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('daily_plans', [
            'id' => $plan->id,
            'status' => 'closed',
        ]);
        $this->assertDatabaseHas('plan_tasks', [
            'id' => $notDone->id,
            'status' => TaskStatus::NotDone->value,
            'not_done_reason' => NotDoneReason::TimeShortage->value,
        ]);
        $this->assertDatabaseHas('plan_tasks', [
            'daily_plan_id' => $plan->id,
            'title' => 'پشتیبانی تلفنی',
            'is_extra' => true,
        ]);
        $this->assertNotNull($plan->refresh()->hours_worked);
    }

    public function test_closing_a_day_requires_a_reason_for_incomplete_tasks(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create([
            'user_id' => $employee->id,
        ]);
        $task = PlanTask::factory()->create([
            'daily_plan_id' => $plan->id,
        ]);

        $this->actingAs($employee)
            ->from(route('daily-plans.close', $plan))
            ->post(route('daily-plans.close.store', $plan), [
                'tasks' => [
                    $task->id => ['status' => TaskStatus::NotDone->value],
                ],
            ])
            ->assertRedirect(route('daily-plans.close', $plan))
            ->assertSessionHasErrors("tasks.{$task->id}.not_done_reason");
    }

    public function test_employee_cannot_view_another_employees_plan(): void
    {
        $owner = User::factory()->employee()->create();
        $other = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create([
            'user_id' => $owner->id,
        ]);

        $this->actingAs($other)
            ->get(route('daily-plans.show', $plan))
            ->assertForbidden();
    }

    public function test_manager_can_view_an_employees_plan(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create([
            'user_id' => $employee->id,
        ]);
        PlanTask::factory()->create([
            'daily_plan_id' => $plan->id,
        ]);

        $this->actingAs($manager)
            ->get(route('daily-plans.show', $plan))
            ->assertOk();
    }
}
