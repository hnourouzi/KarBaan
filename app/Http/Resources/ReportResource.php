<?php

namespace App\Http\Resources;

use App\DTOs\Report\EmployeeReportSummary;
use App\DTOs\Report\ReasonBreakdown;
use App\DTOs\Report\ReportResultData;
use App\Services\JalaliDateFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ReportResultData
 */
class ReportResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ReportResultData $report */
        $report = $this->resource;
        $jalali = app(JalaliDateFormatter::class);

        return [
            'from' => $report->from->toDateString(),
            'to' => $report->to->toDateString(),
            'from_jalali' => $jalali->date($report->from),
            'to_jalali' => $jalali->date($report->to),
            'summary' => [
                'planned_count' => $report->plannedCount,
                'done_count' => $report->doneCount,
                'not_done_count' => $report->notDoneCount,
                'extra_count' => $report->extraCount,
                'completion_rate' => $report->completionRate,
                'hours_worked' => $report->hoursWorked,
            ],
            'reasons' => array_map(
                fn (ReasonBreakdown $item) => [
                    'reason' => $item->reason->value,
                    'reason_label' => $item->reason->label(),
                    'count' => $item->count,
                ],
                $report->reasonTotals,
            ),
            'employees' => $report->employees->map(fn (EmployeeReportSummary $employee) => [
                'user_id' => $employee->userId,
                'user_name' => $employee->userName,
                'planned_count' => $employee->plannedCount,
                'done_count' => $employee->doneCount,
                'not_done_count' => $employee->notDoneCount,
                'extra_count' => $employee->extraCount,
                'completion_rate' => $employee->completionRate,
                'hours_worked' => $employee->hoursWorked,
                'closed_days' => $employee->closedDays,
                'reasons' => array_map(
                    fn (ReasonBreakdown $item) => [
                        'reason' => $item->reason->value,
                        'reason_label' => $item->reason->label(),
                        'count' => $item->count,
                    ],
                    $employee->reasons,
                ),
            ])->values(),
        ];
    }
}
