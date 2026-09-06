<?php

namespace App\Http\Requests\Report;

use App\Models\User;
use Illuminate\Validation\Rule;

class ExportPeriodicReportRequest extends ManagerEmployeeReportRequest
{
    public function authorize(): bool
    {
        if (! parent::authorize()) {
            return false;
        }

        if (! $this->filled('user_id')) {
            return true;
        }

        $employee = User::query()->find($this->integer('user_id'));

        if ($employee === null) {
            return true;
        }

        return $this->user()->can('view', $employee);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')],
        ];
    }
}
