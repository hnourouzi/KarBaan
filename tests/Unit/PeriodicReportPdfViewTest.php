<?php

namespace Tests\Unit;

use App\DTOs\Report\DailyHistoryRow;
use App\DTOs\Report\EmployeeReportSummary;
use App\DTOs\Report\PeriodHistoryData;
use App\Enums\DayHistoryStatus;
use App\Services\Export\PeriodicReportPdfService;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PeriodicReportPdfViewTest extends TestCase
{
    public function test_pdf_blade_renders_persian_text_in_logical_order(): void
    {
        $history = new PeriodHistoryData(
            from: Carbon::parse('2025-09-06'),
            to: Carbon::parse('2025-09-12'),
            summary: new EmployeeReportSummary(
                userId: 1,
                userName: 'علی محمدی',
                plannedCount: 12,
                doneCount: 8,
                notDoneCount: 2,
                extraCount: 1,
                completionRate: 66.7,
                hoursWorked: 40.0,
                closedDays: 5,
                reasons: [],
            ),
            days: collect([
                new DailyHistoryRow(
                    date: Carbon::parse('2025-09-06'),
                    planId: 1,
                    status: DayHistoryStatus::Closed,
                    hoursWorked: 8.0,
                    plannedCount: 4,
                    doneCount: 3,
                    notDoneCount: 1,
                    extraCount: 0,
                ),
            ]),
        );

        $html = view('exports.periodic-report-pdf', app(PeriodicReportPdfService::class)->viewData(
            $history,
            '1405/06/14',
            '1405/06/20',
        ))->render();

        $this->assertStringContainsString('کاربان', $html);
        $this->assertStringContainsString('ساعات کار', $html);
        $this->assertStringContainsString('کل تسک‌ها', $html);
        $this->assertStringContainsString('1405/06/14', $html);
        $this->assertStringNotContainsString('نابراک', $html);
    }
}
