<?php

namespace App\Http\Requests\Investment;

use App\Models\InvestmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validates AJAX calculate and store input for Islamic investments.
 */
class CalculateInvestmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'investment_type_id' => ['required', 'exists:investment_types,id'],
            'calculation_method' => ['nullable', 'string', Rule::in(['annuity', 'reducing'])],
            'principal_amount' => ['required', 'numeric', 'min:0.01'],
            'interest_rate' => ['required', 'numeric', 'min:0'],
            'investment_years' => ['required', 'integer', 'min:1', 'max:30'],
            'start_date' => ['required', 'date'],
            'payment_type' => ['nullable', 'string', Rule::in(['monthly'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $typeId = $this->input('investment_type_id');
            if (! $typeId) {
                return;
            }

            $type = InvestmentType::query()->find($typeId);
            if (! $type) {
                return;
            }

            $code = strtolower((string) ($type->code ?? ''));
            $isHpsm = $code === 'hpsm' || str_contains(strtolower($type->investment_type_name), 'hpsm');

            if ($isHpsm && ! $this->filled('calculation_method')) {
                $validator->errors()->add('calculation_method', 'Calculation method is required for HPSM.');
            }

            if (! $isHpsm && $this->filled('calculation_method')) {
                // Bai-Muajjal ignores method; strip silently by not erroring
            }
        });
    }
}
