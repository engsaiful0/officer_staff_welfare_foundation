<?php

namespace App\Services\Investment\Calculators;

use App\Contracts\Investment\InvestmentCalculatorInterface;
use App\DTO\Investment\InvestmentCalculationResult;
use Carbon\Carbon;
use InvalidArgumentException;

/**
 * Shared helpers for Islamic investment calculators.
 */
abstract class AbstractInvestmentCalculator implements InvestmentCalculatorInterface
{
    public function calculate(array $input): InvestmentCalculationResult
    {
        $normalized = $this->normalizeInput($input);
        $summary = $this->calculateSummary($normalized);
        $schedule = $this->generateSchedule(array_merge($normalized, ['summary' => $summary]));
        $totals = $this->buildTotals($summary, $schedule);

        return new InvestmentCalculationResult($summary, $schedule, $totals);
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array{
     *     principal_amount: float,
     *     annual_rate_percent: float,
     *     monthly_rate: float,
     *     investment_years: int,
     *     term_months: int,
     *     start_date: Carbon,
     *     payment_type: string
     * }
     */
    protected function normalizeInput(array $input): array
    {
        $principal = (float) ($input['principal_amount'] ?? 0);
        $years = (int) ($input['investment_years'] ?? 0);
        $rate = (float) ($input['annual_rate'] ?? $input['interest_rate'] ?? 0);

        if ($principal <= 0) {
            throw new InvalidArgumentException('Principal amount must be greater than zero.');
        }
        if ($years < 1) {
            throw new InvalidArgumentException('Investment years must be at least 1.');
        }
        if ($rate < 0) {
            throw new InvalidArgumentException('Profit/rent rate cannot be negative.');
        }

        // Accept 15 or 0.15 for 15% p.a.
        $annualPercent = $rate > 1 ? $rate : ($rate * 100);

        $paymentType = strtolower((string) ($input['payment_type'] ?? 'monthly'));
        if ($paymentType !== 'monthly') {
            throw new InvalidArgumentException('Only monthly payment type is supported.');
        }

        $termMonths = $years * 12;
        $startDate = Carbon::parse((string) ($input['start_date'] ?? now()->toDateString()))->startOfDay();

        return [
            'principal_amount' => round($principal, 2),
            'annual_rate_percent' => round($annualPercent, 4),
            'monthly_rate' => $annualPercent / 12 / 100,
            'investment_years' => $years,
            'term_months' => $termMonths,
            'start_date' => $startDate,
            'payment_type' => 'monthly',
        ];
    }

    protected function money(float $value): float
    {
        return round($value, 2);
    }

    /**
     * @param  array<string, float|int|string|null>  $summary
     * @param  list<array<string, mixed>>  $schedule
     * @return array<string, float|int>
     */
    protected function buildTotals(array $summary, array $schedule): array
    {
        $principalSum = 0.0;
        $rentSum = 0.0;
        $installmentSum = 0.0;

        foreach ($schedule as $row) {
            $principalSum += (float) $row['principal_amount'];
            $rentSum += (float) $row['rent'];
            $installmentSum += (float) $row['total_amount'];
        }

        return [
            'installment_count' => count($schedule),
            'total_principal' => $this->money($principalSum),
            'total_profit' => $this->money($rentSum),
            'total_payable' => $this->money($installmentSum),
            'selling_price' => $this->money((float) ($summary['selling_price'] ?? $installmentSum)),
        ];
    }

    protected function scheduleDate(Carbon $start, int $installmentNumber): string
    {
        return $start->copy()->addMonths($installmentNumber - 1)->toDateString();
    }
}
