<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class DomainException extends RuntimeException implements ShouldntReport
{
    public function __construct(
        string $message,
        public readonly int $status = 422,
    ) {
        parent::__construct($message);
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return ApiResponse::error($this->getMessage(), $this->status);
        }

        return back()->with('error', $this->getMessage());
    }
}
