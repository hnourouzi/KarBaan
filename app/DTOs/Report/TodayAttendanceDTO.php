<?php

namespace App\DTOs\Report;

use App\Enums\AttendanceStatus;
use Illuminate\Support\Carbon;

readonly class TodayAttendanceDTO
{
    public function __construct(
        public int $userId,
        public string $userName,
        public AttendanceStatus $status,
        public ?Carbon $startedAt,
        public ?Carbon $closedAt,
        public int $plannedCount,
        public int $doneCount,
        public ?int $dailyPlanId,
    ) {}
}
