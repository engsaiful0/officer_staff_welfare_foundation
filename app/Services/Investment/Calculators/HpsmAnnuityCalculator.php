<?php

namespace App\Services\Investment\Calculators;

/**
 * HPSM Annuity (EMI) calculator — Hire Purchase under Shirkatul Melk.
 *
 * Business rules:
 * - Fixed EMI using standard reducing-balance annuity formula
 * - Monthly rent = outstanding principal × monthly rate
 * - Monthly principal = EMI − rent
 * - Ownership ratio = cumulative principal paid / original principal (customer share)
 *
 * Formula:
 * EMI = P × r × (1+r)^n / ((1+r)^n − 1)
 * where r = annual_rate% / 12 / 100, n = years × 12
 *
 * Example (P=100000, 12% p.a., 5 years):
 * r = 0.01; n = 60; EMI ≈ 2224.45
 */
final class HpsmAnnuityCalculator extends AbstractInvestmentCalculator
{
    public function calculateSummary(array $input): array
    {
        $data = isset($input['principal_amount'], $input['term_months'])
            ? $input
            : $this->normalizeInput($input);

        $principal = (float) $data['principal_amount'];
        $r = (float) $data['monthly_rate'];
        $n = (int) $data['term_months'];
        $ratePercent = (float) $data['annual_rate_percent'];
        $years = (int) $data['investment_years'];

        $emi = $this->computeEmi($principal, $r, $n);
        $firstRent = $this->money($principal * $r);
        $firstPrincipal = $this->money($emi - $firstRent);

        // Exact totals from schedule for precision
        $schedule = $this->generateSchedule(array_merge($data, [
            'summary' => [
                'monthly_installment' => $emi,
                'number_of_installments' => $n,
                'principal_amount' => $principal,
            ],
        ]));

        $totalProfit = 0.0;
        foreach ($schedule as $row) {
            $totalProfit += (float) $row['rent'];
        }
        $totalProfit = $this->money($totalProfit);
        $sellingPrice = $this->money($principal + $totalProfit);
        $maturity = $data['start_date']->copy()->addMonths($n)->toDateString();

        return [
            'product' => 'hpsm',
            'calculation_method' => 'annuity',
            'principal_amount' => $principal,
            'annual_rate_percent' => $ratePercent,
            'investment_years' => $years,
            'number_of_installments' => $n,
            'principal_per_installment' => $firstPrincipal,
            'profit_per_installment' => $firstRent,
            'monthly_installment' => $emi,
            'total_profit' => $totalProfit,
            'selling_price' => $sellingPrice,
            'outstanding_balance' => $principal,
            'remaining_principal' => $principal,
            'ownership_ratio' => 0.0,
            'maturity_date' => $maturity,
            'total_payable' => $sellingPrice,
        ];
    }

    public function generateSchedule(array $input): array
    {
        $data = isset($input['principal_amount'], $input['term_months'])
            ? $input
            : $this->normalizeInput($input);

        $principal = (float) $data['principal_amount'];
        $r = (float) $data['monthly_rate'];
        $n = (int) $data['term_months'];

        $emi = isset($input['summary']['monthly_installment'])
            ? (float) $input['summary']['monthly_installment']
            : $this->computeEmi($principal, $r, $n);

        $opening = $principal;
        $cumulativeRent = 0.0;
        $cumulativePrincipal = 0.0;
        $schedule = [];

        for ($i = 1; $i <= $n; $i++) {
            $rent = $this->money($opening * $r);

            if ($i === $n) {
                $principalPart = $this->money($opening);
                $total = $this->money($principalPart + $rent);
            } else {
                $principalPart = $this->money($emi - $rent);
                if ($principalPart > $opening) {
                    $principalPart = $this->money($opening);
                }
                $total = $this->money($principalPart + $rent);
            }

            $ending = $this->money($opening - $principalPart);
            if ($ending < 0) {
                $ending = 0.0;
            }

            $cumulativeRent = $this->money($cumulativeRent + $rent);
            $cumulativePrincipal = $this->money($cumulativePrincipal + $principalPart);
            $ownership = $principal > 0 ? round($cumulativePrincipal / $principal, 4) : 0.0;

            $schedule[] = [
                'installment_number' => $i,
                'schedule_date' => $this->scheduleDate($data['start_date'], $i),
                'beginning_balance' => $this->money($opening),
                'principal_amount' => $principalPart,
                'rent' => $rent,
                'total_amount' => $total,
                'ending_balance' => $ending,
                'cumulative_rent' => $cumulativeRent,
                'outstanding_balance' => $ending,
                'ownership_ratio' => $ownership,
                'status' => 'pending',
            ];

            $opening = $ending;
        }

        return $schedule;
    }

    private function computeEmi(float $principal, float $monthlyRate, int $n): float
    {
        if ($monthlyRate <= 0) {
            return $this->money($principal / $n);
        }

        $factor = pow(1 + $monthlyRate, $n);

        return $this->money($principal * $monthlyRate * $factor / ($factor - 1));
    }
}
