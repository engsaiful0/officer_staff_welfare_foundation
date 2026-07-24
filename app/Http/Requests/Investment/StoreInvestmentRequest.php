<?php

namespace App\Http\Requests\Investment;

use App\Models\InvestmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Validates investment creation. Browser-calculated totals are ignored on save.
 */
class StoreInvestmentRequest extends FormRequest
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
            'member_id' => ['required', 'exists:members,id'],
            'investment_type_id' => ['required', 'exists:investment_types,id'],
            'calculation_method' => ['nullable', 'string', Rule::in(['annuity', 'reducing'])],
            'principal_amount' => ['required', 'numeric', 'min:0.01'],
            'interest_rate' => ['required', 'numeric', 'min:0'],
            'investment_years' => ['required', 'integer', 'min:1', 'max:30'],
            'payment_type' => ['required', 'string', Rule::in(['monthly'])],
            'account_opening_date' => ['required', 'date'],
            'start_date' => ['required', 'date'],
            'gestation_maturity_date' => ['nullable', 'date'],
            'gestation_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            // Optional display-only fields from UI — never trusted for persistence
            'no_of_installments' => ['nullable', 'integer'],
            'principal_amount_per_installment' => ['nullable', 'numeric'],
            'rent' => ['nullable', 'numeric'],
            'total_amount' => ['nullable', 'numeric'],
            'total_rent' => ['nullable', 'numeric'],
            'account_number' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $type = InvestmentType::query()->find($this->input('investment_type_id'));
            if (! $type) {
                return;
            }

            $code = strtolower((string) ($type->code ?? ''));
            $isHpsm = $code === 'hpsm' || str_contains(strtolower($type->investment_type_name), 'hpsm');

            if ($isHpsm && ! $this->filled('calculation_method')) {
                $validator->errors()->add('calculation_method', 'Calculation method is required for HPSM.');
            }

            $opening = $this->input('account_opening_date');
            $start = $this->input('start_date');
            if ($opening && $start && $start < $opening) {
                $validator->errors()->add('start_date', 'Investment start date cannot be before account opening date.');
            }

            $gestation = $this->input('gestation_date') ?? $this->input('gestation_maturity_date');
            if ($gestation && $start && $gestation < $start) {
                $validator->errors()->add('gestation_maturity_date', 'Gestation date cannot be before start date.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'payment_type.in' => 'Only monthly payment type is supported.',
            'principal_amount.min' => 'Principal amount must be greater than zero.',
        ];
    }
}
