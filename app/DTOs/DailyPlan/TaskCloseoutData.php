<?php

namespace App\DTOs\DailyPlan;

use App\Enums\NotDoneReason;
use App\Enums\TaskStatus;

readonly class TaskCloseoutData
{
    public function __construct(
        public int $taskId,
        public TaskStatus $status,
        public ?NotDoneReason $notDoneReason = null,
        public ?string $notDoneNote = null,
    ) {}
}
