<?php

namespace App\DTOs\DailyPlan;

use Illuminate\Support\Carbon;

readonly class CreateDailyPlanData
{
    /**
     * @param  list<string>  $taskTitles
     */
    public function __construct(
        public Carbon $planDate,
        public array $taskTitles,
        public ?string $notes = null,
    ) {}
}
