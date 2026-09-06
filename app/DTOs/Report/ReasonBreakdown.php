<?php

namespace App\DTOs\Report;

use App\Enums\NotDoneReason;

readonly class ReasonBreakdown
{
    public function __construct(
        public NotDoneReason $reason,
        public int $count,
    ) {}
}
