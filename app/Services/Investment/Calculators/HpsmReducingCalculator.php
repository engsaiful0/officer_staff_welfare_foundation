<?php

namespace App\Services\Investment\Calculators;

/**
 * HPSM Reducing Balance calculator — Hire Purchase under Shirkatul Melk.
 *
 * Business rules:
 * - Fixed monthly principal = principal / N
 * - Monthly rent/profit = outstanding principal × annual_rate% / 12 / 100
 * - Monthly installment = principal + rent (declines over time)
 * - Ownership ratio = cumulative principal paid / original principal
 *
 * Formula example (P=100000, 12% p.a., 5 years, N=60):
 * - Principal/mo ≈ 1666.67
 * - Month 1 rent = 100000 × 12 / 1200 = 1000
 * - Month 1 installment ≈ 2666.67
 * - Month 2 rent on 98333.33, etc. (profit decreases monthly)
 */
final class HpsmReducingCalculator extends AbstractInvestmentCalculator
{
    public function calculateSummary(array $input): array
    {
        $data = isset($input['principal_amount'], $input['term_months'])
            ? $input
            : $this->normalizeInput($input);

        $principal = (float) $data['principal_amount'];
        $ratePercent = (float) $data['annual_rate_percent'];
        $years = (int) $data['investment_years'];
        $n = (int) $data['term_months'];
        $monthlyPrincipal = $this->money($principal / $n);
        $firstRent = $this->money($principal * $ratePercent / 1200);
        $firstEmi = $this->money($monthlyPrincipal + $firstRent);

        $schedule = $this->generateSchedule($data);
        $totalProfit = 0.0;
        foreach ($schedule as $row) {
            $totalProfit += (float) $row['rent'];
        }
        $totalProfit = $this->money($totalProfit);
        $sellingPrice = $this->money($principal + $totalProfit);
        $maturity = $data['start_date']->copy()->addMonths($n)->toDateString();

        return [
            'product' => 'hpsm',
            'calculation_method' => 'reducing',
            'principal_amount' => $principal,
            'annual_rate_percent' => $ratePercent,
            'investment_years' => $years,
            'number_of_installments' => $n,
            'principal_per_installment' => $monthlyPrincipal,
            'profit_per_installment' => $firstRent,
            'monthly_installment' => $firstEmi,
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
        $ratePercent = (float) $data['annual_rate_percent'];
        $n = (int) $data['term_months'];

        $parts = $this->splitEqual($principal, $n);
        $opening = $principal;
        $cumulativeRent = 0.0;
        $cumulativePrincipal = 0.0;
        $schedule = [];

        for ($i = 1; $i <= $n; $i++) {
            $principalPart = $parts[$i];
            $rent = $this->money($opening * $ratePercent / 1200);
            $total = $this->money($principalPart + $rent);
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

    /**
     * @return array<int, float>
     */
    private function splitEqual(float $total, int $parts): array
    {
        $base = $this->money($total / $parts);
        $out = [];
        $acc = 0.0;
        for ($i = 1; $i < $parts; $i++) {
            $out[$i] = $base;
            $acc = $this->money($acc + $base);
        }
        $out[$parts] = $this->money($total - $acc);

        return $out;
    }
}
