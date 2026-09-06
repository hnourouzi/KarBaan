<?php

namespace App\DTOs\PlanTask;

readonly class UpdatePlanTaskData
{
    public function __construct(
        public string $title,
    ) {}
}
