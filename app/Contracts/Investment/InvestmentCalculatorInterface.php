<?php

namespace App\Contracts\Investment;

use App\DTO\Investment\InvestmentCalculationResult;

/**
 * Contract for Islamic investment product calculators.
 *
 * Implementations must be interchangeable (Liskov) and selected via
 * InvestmentCalculatorFactory without changing InvestmentService.
 */
interface InvestmentCalculatorInterface
{
    /**
     * Run full calculation (summary + schedule + totals).
     *
     * @param  array{
     *     principal_amount: float|string,
     *     annual_rate: float|string,
     *     investment_years: int,
     *     start_date: string,
     *     payment_type?: string
     * }  $input
     */
    public function calculate(array $input): InvestmentCalculationResult;

    /**
     * Build the installment schedule only.
     *
     * @param  array<string, mixed>  $input
     * @return list<array<string, mixed>>
     */
    public function generateSchedule(array $input): array;

    /**
     * Build summary totals only.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, float|int|string|null>
     */
    public function calculateSummary(array $input): array;
}
