<?php

namespace App\DTOs\DailyPlan;

readonly class CloseDailyPlanData
{
    /**
     * @param  list<TaskCloseoutData>  $tasks
     * @param  list<ExtraTaskData>  $extras
     */
    public function __construct(
        public array $tasks,
        public array $extras = [],
        public ?string $notes = null,
    ) {}
}
