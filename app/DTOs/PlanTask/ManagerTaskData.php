<?php

namespace App\DTOs\PlanTask;

readonly class ManagerTaskData
{
    public function __construct(
        public string $title,
        public ?string $note = null,
    ) {}
}
