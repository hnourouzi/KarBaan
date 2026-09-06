<?php

namespace App\Services\Export;

use App\DTOs\Report\PeriodHistoryData;
use App\Services\JalaliDateFormatter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use RuntimeException;
use Spatie\Browsershot\Browsershot;

class PeriodicReportPdfService
{
    public function __construct(private JalaliDateFormatter $jalali) {}

    public function download(PeriodHistoryData $history, ?string $jalaliFrom, ?string $jalaliTo): Response
    {
        return match (config('karbaan.pdf_driver', 'browsershot')) {
            'browsershot' => $this->downloadViaBrowsershot($history, $jalaliFrom, $jalaliTo),
            'dompdf' => $this->downloadViaDompdf($history, $jalaliFrom, $jalaliTo),
            default => throw new RuntimeException('PDF driver پشتیبانی نمی‌شود.'),
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function viewData(PeriodHistoryData $history, ?string $jalaliFrom, ?string $jalaliTo): array
    {
        return [
            'history' => $history,
            'jalaliFrom' => $jalaliFrom ?? $this->jalali->date($history->from),
            'jalaliTo' => $jalaliTo ?? $this->jalali->date($history->to),
            'fontRegularDataUri' => $this->fontDataUri('Vazirmatn-Regular.ttf'),
            'fontBoldDataUri' => $this->fontDataUri('Vazirmatn-Bold.ttf'),
        ];
    }

    private function downloadViaBrowsershot(PeriodHistoryData $history, ?string $jalaliFrom, ?string $jalaliTo): Response
    {
        $html = view('exports.periodic-report-pdf', $this->viewData($history, $jalaliFrom, $jalaliTo))->render();

        $browsershot = Browsershot::html($html)
            ->setNodeModulePath(base_path('node_modules'))
            ->newHeadless()
            ->emulateMedia('print')
            ->format('A4')
            ->margins(12, 12, 12, 12)
            ->showBackground()
            ->timeout(120);

        if ($nodeBinary = config('karbaan.node_binary')) {
            $browsershot->setNodeBinary($nodeBinary);
        }

        if ($npmBinary = config('karbaan.npm_binary')) {
            $browsershot->setNpmBinary($npmBinary);
        }

        if ($chromePath = config('karbaan.chrome_path')) {
            $browsershot->setChromePath($chromePath);
        }

        $binary = $browsershot->pdf();

        return $this->pdfResponse($binary, $this->filename($history));
    }

    private function downloadViaDompdf(PeriodHistoryData $history, ?string $jalaliFrom, ?string $jalaliTo): Response
    {
        $pdf = Pdf::loadView('exports.periodic-report-pdf', $this->viewData($history, $jalaliFrom, $jalaliTo))
            ->setPaper('a4', 'portrait');

        return $pdf->download($this->filename($history));
    }

    private function pdfResponse(string $binary, string $filename): Response
    {
        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function filename(PeriodHistoryData $history): string
    {
        return sprintf(
            'report-%d-%s-%s.pdf',
            $history->summary->userId,
            $history->from->format('Y-m-d'),
            $history->to->format('Y-m-d'),
        );
    }

    private function fontDataUri(string $filename): string
    {
        $path = resource_path("fonts/{$filename}");

        if (! file_exists($path)) {
            throw new RuntimeException("Font file missing: {$filename}");
        }

        return 'data:font/truetype;base64,'.base64_encode((string) file_get_contents($path));
    }
}
