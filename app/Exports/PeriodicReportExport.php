<?php

namespace App\Exports;

use App\DTOs\Report\DailyHistoryRow;
use App\DTOs\Report\PeriodHistoryData;
use App\Services\JalaliDateFormatter;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PeriodicReportExport implements FromArray, WithEvents, WithTitle
{
    public function __construct(
        private PeriodHistoryData $history,
        private ?string $jalaliFrom,
        private ?string $jalaliTo,
    ) {}

    public function title(): string
    {
        return 'گزارش دوره‌ای';
    }

    /**
     * @return list<list<string|int|float>>
     */
    public function array(): array
    {
        $jalali = app(JalaliDateFormatter::class);
        $summary = $this->history->summary;
        $from = $this->jalaliFrom ?? $jalali->date($this->history->from);
        $to = $this->jalaliTo ?? $jalali->date($this->history->to);

        $rows = [
            ['گزارش دوره‌ای کارمند'],
            ['کارمند', $summary->userName],
            ['بازه', "از {$from} تا {$to}"],
            [],
            ['خلاصه KPI'],
            ['ساعات کار', (string) $summary->hoursWorked],
            ['کل تسک‌ها', $summary->plannedCount],
            ['انجام‌شده', $summary->doneCount],
            ['انجام‌نشده', $summary->notDoneCount],
            ['تسک‌های اضافه', $summary->extraCount],
            ['درصد تکمیل', $summary->completionRate.'٪'],
            [],
            ['تاریخ', 'روز', 'ساعات', 'برنامه', 'انجام‌شده', 'انجام‌نشده', 'اضافه', 'وضعیت'],
        ];

        foreach ($this->history->days as $day) {
            $rows[] = $this->dayRow($day, $jalali);
        }

        return $rows;
    }

    /**
     * @return list<string|int|float>
     */
    private function dayRow(DailyHistoryRow $day, JalaliDateFormatter $jalali): array
    {
        return [
            $jalali->date($day->date),
            $jalali->dayName($day->date),
            $day->hoursWorked,
            $day->plannedCount,
            $day->doneCount,
            $day->notDoneCount,
            $day->extraCount,
            $day->status->label(),
        ];
    }

    /**
     * @return array<class-string, callable>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:H{$highestRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle('A1:H1')->getFont()->setBold(true);
                $sheet->getStyle('A5:A5')->getFont()->setBold(true);
                $sheet->getStyle('A13:H13')->getFont()->setBold(true);

                $sheet->getStyle('A13:H13')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF5F5F4');
            },
        ];
    }
}
