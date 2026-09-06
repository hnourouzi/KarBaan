<?php

namespace App\Http\Requests\Report;

use App\DTOs\Report\ReportQueryData;
use App\Enums\ReportPeriod;
use App\Http\Requests\Concerns\ResolvesReportRange;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HistoryRequest extends FormRequest
{
    use ResolvesReportRange;

    public function authorize(): bool
    {
        return $this->user()?->can('viewOwnHistory', User::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'period' => $this->input('period', ReportPeriod::Monthly->value),
        ]);

        $this->convertJalaliRangeInputs();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'period' => ['required', Rule::enum(ReportPeriod::class)],
            ...$this->rangeRules(),
        ];
    }

    public function after(): array
    {
        return $this->rangeAfterHooks();
    }

    public function toDto(): ReportQueryData
    {
        [$from, $to] = $this->resolveGregorianRange();

        return new ReportQueryData(
            period: ReportPeriod::from($this->string('period')->toString()),
            from: $from,
            to: $to,
            userId: $this->user()->id,
        );
    }
}
