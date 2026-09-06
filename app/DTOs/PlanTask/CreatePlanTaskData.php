<?php

namespace App\DTOs\PlanTask;

readonly class CreatePlanTaskData
{
    public function __construct(
        public string $title,
        public bool $isExtra = false,
    ) {}
}
