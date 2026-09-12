<?php

namespace App\DTOs\WorkSession;

use App\Models\DailyPlan;

readonly class StartWorkSessionData
{
    public function __construct(
        public DailyPlan $plan,
    ) {}
}
