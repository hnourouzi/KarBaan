<?php

namespace App\Http\Requests\WorkSession;

use App\Models\WorkSession;
use Illuminate\Foundation\Http\FormRequest;

class EndWorkSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var WorkSession|null $session */
        $session = $this->route('workSession');

        if (! $session instanceof WorkSession) {
            return false;
        }

        $session->loadMissing('dailyPlan');

        return $this->user()?->can('update', $session->dailyPlan) ?? false;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [];
    }
}
