<?php

namespace Tests\Feature;

use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_returns_a_token(): void
    {
        User::factory()->create([
            'email' => 'ali@karbaan.test',
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'ali@karbaan.test',
            'password' => 'password',
            'device_name' => 'phpunit',
        ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => ['token', 'token_type', 'user'],
            ]);
    }

    public function test_api_guest_cannot_access_protected_routes(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }

    public function test_authenticated_employee_can_create_and_close_a_plan_via_api(): void
    {
        $employee = User::factory()->employee()->create();

        Sanctum::actingAs($employee);

        $create = $this->postJson('/api/v1/daily-plans', [
            'titles' => ['مستندسازی', 'بازبینی کد'],
        ])->assertCreated();

        $planId = $create->json('data.id');
        $this->assertNotNull($planId);

        $plan = DailyPlan::query()->findOrFail($planId);
        $tasks = $plan->tasks()->orderBy('id')->get();

        $this->postJson("/api/v1/daily-plans/{$planId}/close", [
            'tasks' => [
                $tasks[0]->id => ['status' => TaskStatus::Done->value],
                $tasks[1]->id => [
                    'status' => TaskStatus::NotDone->value,
                    'not_done_reason' => NotDoneReason::Blocked->value,
                ],
            ],
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'closed');
    }

    public function test_employee_cannot_update_another_users_task_via_api(): void
    {
        $owner = User::factory()->employee()->create();
        $other = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $owner->id]);
        $task = PlanTask::factory()->create(['daily_plan_id' => $plan->id]);

        Sanctum::actingAs($other);

        $this->patchJson("/api/v1/plan-tasks/{$task->id}/status", [
            'status' => TaskStatus::Done->value,
        ])->assertForbidden();
    }
}
