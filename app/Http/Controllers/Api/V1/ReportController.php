<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ReportRequest;
use App\Http\Resources\ReportResource;
use App\Http\Responses\ApiResponse;
use App\Services\Contracts\ReportServiceInterface;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __invoke(ReportRequest $request, ReportServiceInterface $reports): JsonResponse
    {
        return ApiResponse::success(
            new ReportResource($reports->generate($request->toDto())),
        );
    }
}
