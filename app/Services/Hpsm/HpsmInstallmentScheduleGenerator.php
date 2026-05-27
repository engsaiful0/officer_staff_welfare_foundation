<?php

namespace App\Services\Hpsm;

use App\Models\HpsmInstallment;
use App\Models\HpsmOpeningAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HpsmInstallmentScheduleGenerator
{
    /**
     * Spread balance_pre_rent:
     * Option 1 — PRE_RENT_STRATEGY_FIRST_ONLY: add all balance_pre_rent to installment 1 only.
     * Option 2 — PRE_RENT_STRATEGY_DISTRIBUTE (default): split balance_pre_rent equally across all months
     *             (last month absorbs rounding remainder).
     */
    public const PRE_RENT_STRATEGY_FIRST_ONLY = 'first_installment_only';

    public const PRE_RENT_STRATEGY_DISTRIBUTE = 'distribute_equal';

    private const PRE_RENT_STRATEGY = self::PRE_RENT_STRATEGY_DISTRIBUTE;

    public function replaceSchedule(HpsmOpeningAccount $account): void
    {
        DB::transaction(function () use ($account) {
            $account->installments()->delete();

            $n = (int) $account->remaining_duration_months;
            if ($n < 1) {
                return;
            }

            $balancePrincipal = $this->strMoney($account->balance_principal);
            $balancePreRent = $this->strMoney($account->balance_pre_rent);
            $currentRentExcel = $this->strMoney($account->current_rent);
            $rate = $this->strMoney($account->annual_profit_rate);

            $principalParts = $this->splitEqualParts($balancePrincipal, $n);
            $preRentParts = $this->preRentParts($balancePreRent, $n);

            $runningPrincipal = $balancePrincipal;
            $openDate = Carbon::parse($account->opening_date)->startOfDay();

            for ($i = 1; $i <= $n; $i++) {
                $principalAmt = $principalParts[$i];
                $openingPrincipal = $runningPrincipal;
                $preRent = $preRentParts[$i];

                $rent = $this->monthlyProfitRent($openingPrincipal, $rate);
                if ($i === 1) {
                    $rent = bcadd($rent, $currentRentExcel, 2);
                }

                $totalInstallment = bcadd(bcadd($principalAmt, $preRent, 2), $rent, 2);
                $closing = bcsub($openingPrincipal, $principalAmt, 2);

                /** @var HpsmInstallment $row */
                $row = $account->installments()->create([
                    'installment_no' => $i,
                    'installment_date' => $openDate->copy()->addMonths($i - 1)->toDateString(),
                    'opening_principal' => $openingPrincipal,
                    'principal_amount' => $principalAmt,
                    'pre_rent_amount' => $preRent,
                    'rent_amount' => $rent,
                    'total_installment' => $totalInstallment,
                    'closing_principal' => $closing,
                    'principal_paid' => '0.00',
                    'pre_rent_paid' => '0.00',
                    'rent_paid' => '0.00',
                    'paid_amount' => '0.00',
                    'due_amount' => $totalInstallment,
                    'payment_status' => 'pending',
                    'paid_date' => null,
                ]);

                $row->refreshDueSnapshot();

                $runningPrincipal = $closing;
            }
        });
    }

    /**
     * @return array<int, string> 1-indexed parts
     */
    private function splitEqualParts(string $total, int $parts): array
    {
        $base = bcdiv($total, (string) $parts, 2);
        $out = [];
        for ($i = 1; $i < $parts; $i++) {
            $out[$i] = $base;
        }
        $acc = bcmul($base, (string) ($parts - 1), 2);
        $out[$parts] = bcsub($total, $acc, 2);

        return $out;
    }

    /**
     * @return array<int, string>
     */
    private function preRentParts(string $balancePreRent, int $n): array
    {
        if (self::PRE_RENT_STRATEGY === self::PRE_RENT_STRATEGY_FIRST_ONLY) {
            $parts = [];
            $parts[1] = $balancePreRent;
            for ($i = 2; $i <= $n; $i++) {
                $parts[$i] = '0.00';
            }

            return $parts;
        }

        return $this->splitEqualParts($balancePreRent, $n);
    }

    /**
     * Reducing profit rent for the period: principal * annual_rate / 12 / 100.
     */
    private function monthlyProfitRent(string $principal, string $annualRatePercent): string
    {
        $numerator = bcmul($principal, $annualRatePercent, 6);

        return bcdiv($numerator, '1200', 2);
    }

    private function strMoney(mixed $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
