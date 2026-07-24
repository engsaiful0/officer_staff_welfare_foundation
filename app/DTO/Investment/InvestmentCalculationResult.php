<?php

namespace App\DTO\Investment;

/**
 * Immutable result of an Islamic investment calculation.
 *
 * Purpose: carry summary figures and installment schedule from calculators
 * to services/controllers without leaking calculator internals.
 *
 * @phpstan-type ScheduleRow array{
 *     installment_number: int,
 *     schedule_date: string,
 *     beginning_balance: float,
 *     principal_amount: float,
 *     rent: float,
 *     total_amount: float,
 *     ending_balance: float,
 *     cumulative_rent: float,
 *     outstanding_balance: float,
 *     ownership_ratio: float|null,
 *     status: string
 * }
 */
final class InvestmentCalculationResult
{
    /**
     * @param  list<ScheduleRow>  $schedule
     * @param  array<string, float|int|string|null>  $summary
     * @param  array<string, float|int>  $totals
     */
    public function __construct(
        public readonly array $summary,
        public readonly array $schedule,
        public readonly array $totals,
    ) {
    }

    /**
     * @return array{summary: array, schedule: list, totals: array}
     */
    public function toArray(): array
    {
        return [
            'summary' => $this->summary,
            'schedule' => $this->schedule,
            'totals' => $this->totals,
        ];
    }
}
