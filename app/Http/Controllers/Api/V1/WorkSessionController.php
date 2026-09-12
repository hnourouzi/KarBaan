<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkSession\EndWorkSessionRequest;
use App\Http\Requests\WorkSession\StartWorkSessionRequest;
use App\Http\Resources\WorkSessionResource;
use App\Http\Responses\ApiResponse;
use App\Models\WorkSession;
use App\Services\Contracts\WorkSessionServiceInterface;
use Illuminate\Http\JsonResponse;

class WorkSessionController extends Controller
{
    public function __construct(private WorkSessionServiceInterface $sessions) {}

    public function store(StartWorkSessionRequest $request): JsonResponse
    {
        $session = $this->sessions->start($request->toDto()->plan, now());

        return ApiResponse::success(
            WorkSessionResource::make($session),
            'جلسه کاری شروع شد.',
            201,
        );
    }

    public function end(EndWorkSessionRequest $request, WorkSession $workSession): JsonResponse
    {
        $session = $this->sessions->end($workSession, now());

        return ApiResponse::success(
            WorkSessionResource::make($session),
            'جلسه کاری بسته شد.',
        );
    }
}
