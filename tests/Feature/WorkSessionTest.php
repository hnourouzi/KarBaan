<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\TaskStatus;
use App\Exceptions\WorkSessionLimitReachedException;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use App\Models\WorkSession;
use App\Services\Contracts\ReportServiceInterface;
use App\Services\Contracts\WorkSessionServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WorkSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_opening_a_plan_creates_the_first_work_session(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->post(route('daily-plans.store'), [
                'titles' => ['بررسی ایمیل'],
            ])
            ->assertRedirect(route('dashboard'));

        $plan = DailyPlan::query()->where('user_id', $employee->id)->first();

        $this->assertNotNull($plan);
        $this->assertDatabaseCount('work_sessions', 1);
        $this->assertDatabaseHas('work_sessions', [
            'daily_plan_id' => $plan->id,
            'ended_at' => null,
        ]);
    }

    public function test_employee_can_end_a_session_and_start_another(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);
        $open = $plan->openWorkSession();

        $this->actingAs($employee)
            ->post(route('work-sessions.end', $open))
            ->assertRedirect();

        $this->assertNotNull($open->refresh()->ended_at);

        $this->actingAs($employee)
            ->post(route('daily-plans.work-sessions.store', $plan))
            ->assertRedirect();

        $this->assertSame(2, $plan->workSessions()->count());
        $this->assertNotNull($plan->refresh()->openWorkSession());
    }

    public function test_employee_cannot_start_a_second_open_session(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);

        $this->actingAs($employee)
            ->from(route('dashboard'))
            ->post(route('daily-plans.work-sessions.store', $plan))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');

        $this->assertSame(1, $plan->workSessions()->whereNull('ended_at')->count());
    }

    public function test_midnight_crossing_session_belongs_to_the_start_day_and_counts_duration(): void
    {
        $employee = User::factory()->employee()->create();

        $this->travelTo('2026-09-12 23:00:00');

        $plan = DailyPlan::factory()->create([
            'user_id' => $employee->id,
            'plan_date' => '2026-09-12',
            'started_at' => now(),
        ]);
        $session = $plan->openWorkSession();

        $this->travelTo('2026-09-13 01:00:00');

        $ended = app(WorkSessionServiceInterface::class)->end($session, now());

        $this->assertSame('2026-09-12', $plan->fresh()->plan_date->toDateString());
        $this->assertSame(2.0, $ended->durationHours());
        $this->assertSame(2.0, (float) $plan->fresh()->hours_worked);
    }

    public function test_confirmed_hours_sum_closed_sessions_only(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);
        $first = $plan->openWorkSession();

        $service = app(WorkSessionServiceInterface::class);
        $service->end($first, $first->started_at->copy()->addHours(3));
        $second = $service->start($plan->refresh(), now());

        $this->assertSame(3.0, (float) $plan->fresh()->hours_worked);
        $this->assertTrue($second->isOpen());
        $this->assertSame(3.0, $service->confirmedHours($plan->fresh()->load('workSessions')));
    }

    public function test_closing_the_day_ends_the_open_session(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);
        $task = PlanTask::factory()->create(['daily_plan_id' => $plan->id]);

        $this->actingAs($employee)
            ->post(route('daily-plans.close.store', $plan), [
                'tasks' => [
                    $task->id => ['status' => TaskStatus::Done->value],
                ],
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertTrue($plan->refresh()->isClosed());
        $this->assertTrue($plan->workSessions()->whereNull('ended_at')->doesntExist());
        $this->assertNotNull($plan->hours_worked);
    }

    public function test_attendance_overview_marks_break_between_sessions(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);
        $open = $plan->openWorkSession();

        app(WorkSessionServiceInterface::class)->end($open, $open->started_at->copy()->addHour());

        $overview = app(ReportServiceInterface::class)->getTodayAttendanceOverview();

        $this->assertSame(
            AttendanceStatus::OnBreak,
            $overview->firstWhere('userId', $employee->id)->status,
        );
    }

    public function test_api_can_start_and_end_a_work_session(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);
        $open = $plan->openWorkSession();

        Sanctum::actingAs($employee);

        $this->postJson("/api/v1/work-sessions/{$open->id}/end")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/api/v1/work-sessions/start', [
            'daily_plan_id' => $plan->id,
        ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertSame(2, $plan->workSessions()->count());
    }

    public function test_employee_cannot_end_another_users_session(): void
    {
        $owner = User::factory()->employee()->create();
        $other = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $owner->id]);
        $session = $plan->openWorkSession();

        $this->actingAs($other)
            ->post(route('work-sessions.end', $session))
            ->assertForbidden();
    }

    public function test_rejects_more_than_ten_sessions_in_a_day(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->create(['user_id' => $employee->id]);
        $service = app(WorkSessionServiceInterface::class);
        $open = $plan->openWorkSession();
        $service->end($open, $open->started_at->copy()->addMinutes(10));

        for ($i = 0; $i < 9; $i++) {
            $started = now()->addMinutes(($i + 1) * 20);
            $session = $service->start($plan->refresh(), $started);
            $service->end($session, $started->copy()->addMinutes(5));
        }

        $this->expectException(WorkSessionLimitReachedException::class);
        $service->start($plan->refresh(), now()->addHours(5));
    }

    public function test_day_detail_includes_all_sessions(): void
    {
        $employee = User::factory()->employee()->create();
        $plan = DailyPlan::factory()->closed()->create(['user_id' => $employee->id]);
        WorkSession::factory()->closed()->create([
            'daily_plan_id' => $plan->id,
            'started_at' => $plan->started_at->copy()->addHours(10),
            'ended_at' => $plan->started_at->copy()->addHours(12),
        ]);

        $this->actingAs($employee)
            ->getJson(route('reports.days.show', $plan))
            ->assertOk()
            ->assertJsonPath('data.sessions.0.started_at', $plan->started_at->format('H:i'))
            ->assertJsonCount(2, 'data.sessions');
    }
}
