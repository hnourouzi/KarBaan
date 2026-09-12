<?php

namespace App\Http\Resources;

use App\Models\WorkSession;
use App\Services\JalaliDateFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin WorkSession
 */
class WorkSessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $jalali = app(JalaliDateFormatter::class);

        return [
            'id' => $this->id,
            'started_at' => $jalali->time($this->started_at),
            'ended_at' => $this->isOpen() ? null : $jalali->time($this->ended_at),
            'started_at_iso' => $this->started_at?->toIso8601String(),
            'ended_at_iso' => $this->ended_at?->toIso8601String(),
            'hours' => $this->isClosed() ? $this->durationHours() : null,
            'is_open' => $this->isOpen(),
        ];
    }
}
