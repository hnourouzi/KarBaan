<?php

namespace App\Http\Controllers\Web;

use App\Exports\PeriodicReportExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ExportPeriodicReportRequest;
use App\Services\Contracts\ReportServiceInterface;
use App\Services\Export\PeriodicReportPdfService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class PeriodicReportExportController extends Controller
{
    public function excel(ExportPeriodicReportRequest $request, ReportServiceInterface $reports): BinaryFileResponse
    {
        $history = $reports->history($request->toDto());

        $filename = sprintf(
            'report-%d-%s-%s.xlsx',
            $history->summary->userId,
            $history->from->format('Y-m-d'),
            $history->to->format('Y-m-d'),
        );

        return Excel::download(
            new PeriodicReportExport($history, $request->jalaliFrom(), $request->jalaliTo()),
            $filename,
        );
    }

    public function pdf(
        ExportPeriodicReportRequest $request,
        ReportServiceInterface $reports,
        PeriodicReportPdfService $pdf,
    ): Response {
        $history = $reports->history($request->toDto());

        return $pdf->download($history, $request->jalaliFrom(), $request->jalaliTo());
    }
}
