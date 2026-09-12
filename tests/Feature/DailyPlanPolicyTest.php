<?php

namespace Tests\Feature;

use App\Models\DailyPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyPlanPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_an_open_plan(): void
    {
        $owner = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $owner->id]);

        $this->assertTrue($owner->can('update', $plan));
        $this->assertTrue($owner->can('close', $plan));
    }

    public function test_owner_cannot_update_a_closed_plan(): void
    {
        $owner = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->closed()->create(['user_id' => $owner->id]);

        $this->assertFalse($owner->can('update', $plan));
        $this->assertFalse($owner->can('close', $plan));
    }

    public function test_other_employee_cannot_update_a_plan(): void
    {
        $owner = User::factory()->employee()->create();
        $other = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($other->can('update', $plan));
        $this->assertFalse($other->can('view', $plan));
    }

    public function test_manager_can_view_but_not_close_another_plan(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);

        $this->assertTrue($manager->can('view', $plan));
        $this->assertFalse($manager->can('close', $plan));
        $this->assertTrue($manager->can('assignTask', $plan));
    }

    public function test_manager_cannot_assign_a_task_to_a_closed_plan(): void
    {
        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->closed()->create(['user_id' => $employee->id]);

        $this->assertFalse($manager->can('assignTask', $plan));
    }

    public function test_employee_cannot_assign_a_manager_task(): void
    {
        $owner = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $owner->id]);

        $this->assertFalse($owner->can('assignTask', $plan));
    }
}
