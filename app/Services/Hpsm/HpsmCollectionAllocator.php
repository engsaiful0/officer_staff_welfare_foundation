<?php

namespace App\Services\Hpsm;

use App\Models\HpsmCollection;
use App\Models\HpsmInstallment;
use App\Models\HpsmOpeningAccount;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HpsmCollectionAllocator
{
    /**
     * Apply a collection payment starting from the chosen installment.
     * Allocation order per installment: pre-rent → rent → principal.
     * Spills to later installments until the receipt amount is exhausted.
     */
    public function apply(
        HpsmOpeningAccount $account,
        HpsmInstallment $startingInstallment,
        string $totalCollected,
        array $attributes
    ): HpsmCollection {
        return DB::transaction(function () use ($account, $startingInstallment, $totalCollected, $attributes) {
            $account = HpsmOpeningAccount::whereKey($account->id)->lockForUpdate()->firstOrFail();
            $startingInstallment->refresh();

            $remaining = $this->money($totalCollected);

            $this->assertNoEarlierUnpaid($account, $startingInstallment);

            if (bccomp($startingInstallment->totalDue(), '0', 2) !== 1) {
                throw new RuntimeException('Selected installment has no outstanding due amount.');
            }

            $totalPre = '0.00';
            $totalRent = '0.00';
            $totalPrincipal = '0.00';

            $installments = $account->installments()
                ->where('installment_no', '>=', $startingInstallment->installment_no)
                ->orderBy('installment_no')
                ->lockForUpdate()
                ->get();

            foreach ($installments as $installment) {
                if (bccomp($remaining, '0', 2) !== 1) {
                    break;
                }

                if ($installment->payment_status === 'paid') {
                    continue;
                }

                $installment->refresh();

                $preDue = $this->maxZero($installment->preRentDue());
                $rentDue = $this->maxZero($installment->rentDue());
                $princDue = $this->maxZero($installment->principalDue());

                $takePre = $this->takeFromRemaining($remaining, $preDue);
                $takeRent = $this->takeFromRemaining($remaining, $rentDue);
                $takePrinc = $this->takeFromRemaining($remaining, $princDue);

                if (bccomp($takePre, '0', 2) !== 1
                    && bccomp($takeRent, '0', 2) !== 1
                    && bccomp($takePrinc, '0', 2) !== 1) {
                    continue;
                }

                $installment->pre_rent_paid = bcadd((string) $installment->pre_rent_paid, $takePre, 2);
                $installment->rent_paid = bcadd((string) $installment->rent_paid, $takeRent, 2);
                $installment->principal_paid = bcadd((string) $installment->principal_paid, $takePrinc, 2);
                $installment->paid_amount = bcadd(
                    bcadd((string) $installment->pre_rent_paid, (string) $installment->rent_paid, 2),
                    (string) $installment->principal_paid,
                    2
                );
                $installment->save();
                $installment->refreshDueSnapshot();

                $totalPre = bcadd($totalPre, $takePre, 2);
                $totalRent = bcadd($totalRent, $takeRent, 2);
                $totalPrincipal = bcadd($totalPrincipal, $takePrinc, 2);
            }

            if (bccomp($remaining, '0', 2) === 1) {
                throw new RuntimeException('Collected amount exceeds total outstanding dues for this account from the selected installment onward.');
            }

            $account->refresh();
            $account->current_outstanding_principal = bcsub(
                $this->money($account->current_outstanding_principal),
                $totalPrincipal,
                2
            );
            if (bccomp($account->current_outstanding_principal, '0', 2) === -1) {
                $account->current_outstanding_principal = '0.00';
            }

            if (bccomp($account->current_outstanding_principal, '0', 2) !== 1) {
                $account->status = 'completed';
            }

            $account->save();

            $collection = new HpsmCollection(array_merge([
                'hpsm_opening_account_id' => $account->id,
                'hpsm_installment_id' => $startingInstallment->id,
                'principal_collected' => $totalPrincipal,
                'pre_rent_collected' => $totalPre,
                'rent_collected' => $totalRent,
                'total_collected' => $this->money($totalCollected),
            ], $attributes));
            $collection->save();

            return $collection;
        });
    }

    private function assertNoEarlierUnpaid(HpsmOpeningAccount $account, HpsmInstallment $start): void
    {
        $blocked = $account->installments()
            ->where('installment_no', '<', $start->installment_no)
            ->get()
            ->contains(function (HpsmInstallment $i) {
                return bccomp($i->totalDue(), '0', 2) === 1;
            });

        if ($blocked) {
            throw new RuntimeException('Earlier installments still have balances. Collect those before this installment.');
        }
    }

    private function takeFromRemaining(string &$remaining, string $due): string
    {
        if (bccomp($due, '0', 2) !== 1) {
            return '0.00';
        }
        if (bccomp($remaining, '0', 2) !== 1) {
            return '0.00';
        }

        if (bccomp($remaining, $due, 2) !== -1) {
            $taken = $due;
            $remaining = bcsub($remaining, $due, 2);
        } else {
            $taken = $remaining;
            $remaining = '0.00';
        }

        return $taken;
    }

    private function maxZero(string $value): string
    {
        return bccomp($value, '0', 2) === 1 ? $value : '0.00';
    }

    private function money(mixed $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
