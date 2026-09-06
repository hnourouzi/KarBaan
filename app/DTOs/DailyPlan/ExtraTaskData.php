<?php

namespace App\DTOs\DailyPlan;

readonly class ExtraTaskData
{
    public function __construct(
        public string $title,
    ) {}
}
