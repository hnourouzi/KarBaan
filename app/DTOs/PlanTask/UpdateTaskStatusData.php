<?php

namespace App\DTOs\PlanTask;

use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;

readonly class UpdateTaskStatusData
{
    public function __construct(
        public TaskStatus $status,
        public ?NotDoneReason $notDoneReason = null,
        public ?string $notDoneNote = null,
    ) {}
}
