<?php

namespace Tests\Feature;

use App\Enums\TaskStatus;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ManagerAssignedTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_add_a_task_to_an_employees_open_plan(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);

        $this->actingAs($manager)
            ->post(route('daily-plans.manager-tasks.store', $plan), [
                'title' => 'پیگیری قرارداد',
                'note' => 'قبل از ظهر انجام شود',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('plan_tasks', [
            'daily_plan_id' => $plan->id,
            'title' => 'پیگیری قرارداد',
            'assigned_by' => $manager->id,
            'assigned_note' => 'قبل از ظهر انجام شود',
            'status' => TaskStatus::Planned->value,
            'is_extra' => false,
        ]);
    }

    public function test_employee_sees_manager_assigned_task_on_their_dashboard(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);
        PlanTask::factory()->create([
            'daily_plan_id' => $plan->id,
            'title' => 'گزارش هفتگی',
            'assigned_by' => $manager->id,
        ]);

        $this->actingAs($employee)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('گزارش هفتگی')
            ->assertSee('توسط مدیر اضافه شده')
            ->assertSee('1 وظیفه جدید توسط مدیر اضافه شده');
    }

    public function test_employee_cannot_add_a_manager_tagged_task(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);

        $this->actingAs($employee)
            ->post(route('daily-plans.manager-tasks.store', $plan), [
                'title' => 'کار خودساخته',
            ])
            ->assertForbidden();
    }

    public function test_employee_cannot_add_a_manager_task_to_another_plan(): void
    {
        $owner = User::factory()->employee()->create();
        $other = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->post(route('daily-plans.manager-tasks.store', $plan), [
                'title' => 'وظیفه غیرمجاز',
            ])
            ->assertForbidden();
    }

    public function test_manager_cannot_add_a_task_to_a_closed_plan(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->closed()->create(['user_id' => $employee->id]);

        $this->actingAs($manager)
            ->post(route('daily-plans.manager-tasks.store', $plan), [
                'title' => 'کار دیرهنگام',
            ])
            ->assertForbidden();
    }

    public function test_manager_can_add_a_task_via_api(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);

        Sanctum::actingAs($manager);

        $this->postJson("/api/v1/daily-plans/{$plan->id}/manager-tasks", [
            'title' => 'بازبینی مستندات',
        ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'بازبینی مستندات')
            ->assertJsonPath('data.assigned_by_manager', true);
    }

    public function test_day_detail_allows_manager_to_assign_on_open_plans(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);

        $this->actingAs($manager)
            ->getJson(route('reports.days.show', $plan))
            ->assertOk()
            ->assertJsonPath('data.can_assign_task', true);
    }

    public function test_regular_employee_task_is_not_manager_tagged(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);

        $this->actingAs($employee)
            ->post(route('daily-plans.tasks.store', $plan), [
                'title' => 'کار عادی',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('plan_tasks', [
            'daily_plan_id' => $plan->id,
            'title' => 'کار عادی',
            'assigned_by' => null,
        ]);
    }
}
