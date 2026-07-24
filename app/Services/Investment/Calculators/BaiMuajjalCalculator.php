<?php

namespace App\Services\Investment\Calculators;

/**
 * Bai-Muajjal calculator (Islamic deferred-payment sale).
 *
 * Business rules (IBBL-style equal installment Bai-Muajjal):
 * - Fixed selling price = principal + total profit
 * - Total profit = principal × annual_rate% × years / 100 (flat markup)
 * - Equal monthly installments = selling_price / N
 * - Each month: equal principal share + equal profit share
 *
 * Formula example (P=100000, rate=12%, years=5):
 * - Profit = 100000 × 12 × 5 / 100 = 60000
 * - Selling price = 160000
 * - N = 60; EMI = 2666.67
 * - Principal/mo ≈ 1666.67; Profit/mo = 1000.00
 */
final class BaiMuajjalCalculator extends AbstractInvestmentCalculator
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

        $totalProfit = $this->money($principal * $ratePercent * $years / 100);
        $sellingPrice = $this->money($principal + $totalProfit);
        $emi = $this->money($sellingPrice / $n);
        $principalPer = $this->money($principal / $n);
        $profitPer = $this->money($totalProfit / $n);
        $maturity = $data['start_date']->copy()->addMonths($n)->toDateString();

        return [
            'product' => 'bai_muajjal',
            'calculation_method' => null,
            'principal_amount' => $principal,
            'annual_rate_percent' => $ratePercent,
            'investment_years' => $years,
            'number_of_installments' => $n,
            'principal_per_installment' => $principalPer,
            'profit_per_installment' => $profitPer,
            'monthly_installment' => $emi,
            'total_profit' => $totalProfit,
            'selling_price' => $sellingPrice,
            'outstanding_balance' => $sellingPrice,
            'remaining_principal' => $principal,
            'ownership_ratio' => null,
            'maturity_date' => $maturity,
            'total_payable' => $sellingPrice,
        ];
    }

    public function generateSchedule(array $input): array
    {
        $data = isset($input['principal_amount'], $input['term_months'])
            ? $input
            : $this->normalizeInput($input);

        $summary = $input['summary'] ?? $this->calculateSummary($data);
        $n = (int) $summary['number_of_installments'];
        $principal = (float) $summary['principal_amount'];
        $totalProfit = (float) $summary['total_profit'];
        $sellingPrice = (float) $summary['selling_price'];

        $principalParts = $this->splitEqual($principal, $n);
        $profitParts = $this->splitEqual($totalProfit, $n);

        $openingPrincipal = $principal;
        $openingPayable = $sellingPrice;
        $cumulativeRent = 0.0;
        $schedule = [];

        for ($i = 1; $i <= $n; $i++) {
            $p = $principalParts[$i];
            $r = $profitParts[$i];
            $total = $this->money($p + $r);
            $endingPrincipal = $this->money($openingPrincipal - $p);
            $endingPayable = $this->money($openingPayable - $total);
            $cumulativeRent = $this->money($cumulativeRent + $r);

            $schedule[] = [
                'installment_number' => $i,
                'schedule_date' => $this->scheduleDate($data['start_date'], $i),
                'beginning_balance' => $openingPrincipal,
                'principal_amount' => $p,
                'rent' => $r,
                'total_amount' => $total,
                'ending_balance' => max(0, $endingPrincipal),
                'cumulative_rent' => $cumulativeRent,
                'outstanding_balance' => max(0, $endingPayable),
                'ownership_ratio' => null,
                'status' => 'pending',
            ];

            $openingPrincipal = max(0, $endingPrincipal);
            $openingPayable = max(0, $endingPayable);
        }

        return $schedule;
    }

    /**
     * @return array<int, float> 1-indexed parts summing exactly to $total
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
