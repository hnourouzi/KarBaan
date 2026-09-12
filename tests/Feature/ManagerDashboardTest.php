<?php

namespace Tests\Feature;

use App\Enums\AttendanceStatus;
use App\Enums\DailyPlanStatus;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use App\Services\Contracts\ReportServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_dashboard_shows_today_attendance_for_all_employees(): void
    {
        $manager = User::factory()->manager()->create();
        $started = User::factory()->employee()->create(['name' => 'علی شروع‌شده']);
        $finished = User::factory()->employee()->create(['name' => 'سارا پایان‌یافته']);
        $notStarted = User::factory()->employee()->create(['name' => 'رضا بدون برنامه']);

        $openPlan = DailyPlan::factory()->create([
            'user_id' => $started->id,
            'plan_date' => now()->toDateString(),
            'status' => DailyPlanStatus::Open,
            'started_at' => now()->setTime(8, 30),
        ]);
        PlanTask::factory()->done()->create(['daily_plan_id' => $openPlan->id]);
        PlanTask::factory()->create(['daily_plan_id' => $openPlan->id]);

        $closedPlan = DailyPlan::factory()->closed()->create([
            'user_id' => $finished->id,
            'plan_date' => now()->toDateString(),
            'started_at' => now()->setTime(8, 0),
            'closed_at' => now()->setTime(16, 0),
        ]);
        PlanTask::factory()->done()->count(2)->create(['daily_plan_id' => $closedPlan->id]);

        $this->actingAs($manager)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('حضور امروز')
            ->assertSee('علی شروع‌شده')
            ->assertSee('سارا پایان‌یافته')
            ->assertSee('رضا بدون برنامه')
            ->assertSee('در حال کار')
            ->assertSee('پایان یافته')
            ->assertSee('شروع نشده')
            ->assertSee('data-open-day="'.$openPlan->id.'"', false)
            ->assertSee('data-open-day="'.$closedPlan->id.'"', false)
            ->assertDontSee('data-open-day=""', false);
    }

    public function test_today_attendance_service_returns_expected_statuses(): void
    {
        $started = User::factory()->employee()->create();
        $finished = User::factory()->employee()->create();
        User::factory()->employee()->create();

        DailyPlan::factory()->create([
            'user_id' => $started->id,
            'plan_date' => now()->toDateString(),
            'status' => DailyPlanStatus::Open,
        ]);

        DailyPlan::factory()->closed()->create([
            'user_id' => $finished->id,
            'plan_date' => now()->toDateString(),
        ]);

        $overview = app(ReportServiceInterface::class)->getTodayAttendanceOverview();

        $this->assertCount(3, $overview);
        $this->assertSame(AttendanceStatus::Started, $overview->firstWhere('userId', $started->id)->status);
        $this->assertSame(AttendanceStatus::Finished, $overview->firstWhere('userId', $finished->id)->status);
        $this->assertSame(AttendanceStatus::NotStarted, $overview->first(fn ($row) => $row->status === AttendanceStatus::NotStarted)->status);
    }

    public function test_employee_dashboard_does_not_show_today_attendance_section(): void
    {
        $employee = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('حضور امروز');
    }
}
