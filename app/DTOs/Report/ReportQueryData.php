<?php

namespace App\DTOs\Report;

use App\Enums\ReportPeriod;
use Illuminate\Support\Carbon;

readonly class ReportQueryData
{
    public function __construct(
        public ReportPeriod $period,
        public Carbon $from,
        public Carbon $to,
        public ?int $userId = null,
    ) {}
}
