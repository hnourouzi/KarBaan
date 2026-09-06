<?php

namespace Tests\Feature;

use App\Enums\ReportPeriod;
use App\Models\DailyPlan;
use App\Models\PlanTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class PeriodicReportExportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string|int>
     */
    private function exportQuery(User $employee): array
    {
        return [
            'period' => ReportPeriod::Weekly->value,
            'user_id' => $employee->id,
        ];
    }

    public function test_manager_can_export_periodic_report_as_excel(): void
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

        $response = $this->actingAs($manager)
            ->get(route('reports.employee.export.excel', $this->exportQuery($employee)));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $temp = tempnam(sys_get_temp_dir(), 'report');
        file_put_contents($temp, file_get_contents($response->getFile()->getPathname()));

        $sheet = IOFactory::load($temp)->getActiveSheet();
        $flat = collect($sheet->toArray())->flatten()->filter()->values()->all();

        $this->assertContains('گزارش دوره‌ای کارمند', $flat);
        $this->assertContains('سارا احمدی', $flat);
        $this->assertContains('تاریخ', $flat);
        $this->assertContains('وضعیت', $flat);
        $this->assertTrue($sheet->getRightToLeft());

        unlink($temp);
    }

    public function test_manager_can_export_periodic_report_as_pdf(): void
    {
        config(['karbaan.pdf_driver' => 'browsershot']);

        $manager = User::factory()->manager()->create();
        $employee = User::factory()->employee()->create(['name' => 'علی محمدی']);
        $plan = DailyPlan::factory()->closed()->create([
            'user_id' => $employee->id,
            'plan_date' => now()->toDateString(),
            'hours_worked' => 7.5,
        ]);
        PlanTask::factory()->done()->create(['daily_plan_id' => $plan->id]);

        $response = $this->actingAs($manager)
            ->get(route('reports.employee.export.pdf', $this->exportQuery($employee)));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');

        $content = $response->getContent();
        $this->assertStringStartsWith('%PDF', $content);
        $this->assertGreaterThan(10_000, strlen($content));
    }

    public function test_employee_cannot_export_periodic_report(): void
    {
        $employee = User::factory()->employee()->create();
        $other = User::factory()->employee()->create();

        $this->actingAs($employee)
            ->get(route('reports.employee.export.excel', $this->exportQuery($other)))
            ->assertForbidden();

        $this->actingAs($employee)
            ->get(route('reports.employee.export.pdf', $this->exportQuery($other)))
            ->assertForbidden();
    }

    public function test_export_requires_a_valid_employee(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('reports.employee.export.excel', [
                'period' => ReportPeriod::Weekly->value,
                'user_id' => 99999,
            ]))
            ->assertSessionHasErrors('user_id');
    }
}
