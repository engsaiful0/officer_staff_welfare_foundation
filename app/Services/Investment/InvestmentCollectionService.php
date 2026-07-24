<?php

namespace App\Services\Investment;

use App\Models\Investment;
use App\Models\InvestmentAccount;
use App\Models\InvestmentInstallment;
use App\Models\LedgerEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Processes installment collections for Bai-Muajjal / HPSM investments.
 *
 * Business rules:
 * - Collect only against scheduled installment amounts (principal + rent from schedule)
 * - Earlier installments must be settled before later ones
 * - Outstanding principal reduces only by principal_amount collected
 * - Updates investment.remaining_principal and account balances
 */
class InvestmentCollectionService
{
    /**
     * Build account + pending installment payload for the collection UI.
     *
     * @return array<string, mixed>
     */
    public function getAccountCollectionData(InvestmentAccount $account, bool $includePaid = false): array
    {
        $account->loadMissing(['investment.member', 'investment.investmentType']);

        $statuses = $includePaid ? ['pending', 'paid', 'overdue'] : ['pending', 'overdue'];

        $installments = $account->investment->installments()
            ->whereIn('status', $statuses)
            ->orderBy('installment_number')
            ->get()
            ->map(function (InvestmentInstallment $installment) {
                $daysLate = $installment->getDaysLate();
                $fine = $installment->status === 'paid'
                    ? (float) ($installment->fine_amount ?? 0)
                    : $installment->calculateFine();
                $scheduleTotal = (float) $installment->principal_amount + (float) $installment->rent;
                $totalDue = $installment->status === 'paid'
                    ? (float) $installment->total_amount
                    : $scheduleTotal + $fine;

                return [
                    'id' => $installment->id,
                    'installment_number' => $installment->installment_number,
                    'schedule_date' => $installment->schedule_date->format('Y-m-d'),
                    'schedule_date_formatted' => $installment->schedule_date->format('M d, Y'),
                    'beginning_balance' => (float) $installment->beginning_balance,
                    'principal_amount' => (float) $installment->principal_amount,
                    'rent' => (float) $installment->rent,
                    'ending_balance' => (float) $installment->ending_balance,
                    'fine_amount' => (float) $fine,
                    'schedule_total' => round($scheduleTotal, 2),
                    'total_amount' => round($totalDue, 2),
                    'days_late' => $daysLate,
                    'is_overdue' => $installment->isOverdue(),
                    'month_name' => $installment->schedule_date->format('F Y'),
                    'status' => $installment->status,
                ];
            })
            ->values()
            ->all();

        $investment = $account->investment;
        $member = $investment->member;

        return [
            'account' => [
                'id' => $account->id,
                'account_number' => $account->account_number,
                'member_name' => $member?->name,
                'member_id' => $member?->member_unique_id ?? $member?->unique_id,
                'current_balance' => (float) ($investment->remaining_principal ?? $account->current_balance),
                'principal_amount' => (float) $investment->principal_amount,
                'selling_price' => (float) ($investment->selling_price ?? 0),
                'profit_amount' => (float) ($investment->profit_amount ?? 0),
                'emi_amount' => (float) ($investment->emi_amount ?? 0),
                'remaining_principal' => (float) ($investment->remaining_principal ?? $investment->principal_amount),
                'product_name' => $investment->product_name
                    ?? $investment->investmentType?->investment_type_name,
                'product_code' => $investment->investmentType?->code,
                'calculation_method' => $investment->calculation_method,
                'calculation_method_label' => $this->methodLabel($investment->calculation_method),
            ],
            'installments' => $installments,
        ];
    }

    /**
     * Collect a scheduled installment payment.
     *
     * @param  array<string, mixed>  $input
     * @return array{installment: InvestmentInstallment, receipt_number: string, fine: float, days_late: int, net_paid: float}
     */
    public function collect(InvestmentAccount $account, InvestmentInstallment $installment, array $input): array
    {
        return DB::transaction(function () use ($account, $installment, $input) {
            $account = InvestmentAccount::with('investment.investmentType')
                ->whereKey($account->id)
                ->lockForUpdate()
                ->firstOrFail();

            $installment = InvestmentInstallment::whereKey($installment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ((int) $installment->investment_id !== (int) $account->investment_id) {
                throw new InvalidArgumentException('Installment does not belong to this investment account.');
            }

            if ($installment->status === 'paid') {
                throw new RuntimeException('This installment has already been paid.');
            }

            $this->assertSequentialCollection($account->investment_id, (int) $installment->installment_number);

            $paidDate = Carbon::parse($input['paid_date']);
            $fine = $installment->calculateFine($paidDate);
            $daysLate = $installment->getDaysLate($paidDate);
            $scheduleTotal = (float) $installment->principal_amount + (float) $installment->rent;
            $baseTotal = round($scheduleTotal + $fine, 2);
            $discountAmount = round((float) ($input['discount_amount'] ?? 0), 2);
            $netPaidAmount = max(0, round($baseTotal - $discountAmount, 2));
            $receiptNumber = $this->generateReceiptNumber();

            $installment->update([
                'status' => 'paid',
                'paid_date' => $paidDate,
                'fine_amount' => $fine,
                'discount_amount' => $discountAmount,
                'total_amount' => $baseTotal,
                'paid_by' => Auth::id(),
                'payment_method_id' => $input['payment_method_id'],
                'transaction_reference' => $input['transaction_reference'] ?? null,
                'receipt_number' => $receiptNumber,
                'bank_name' => $input['bank_name'] ?? null,
                'check_number' => $input['check_number'] ?? null,
                'notes' => $input['notes'] ?? null,
            ]);

            $investment = $account->investment;
            $previousBalance = $this->currentOutstandingPrincipal($investment);
            $newBalance = max(0, round($previousBalance - (float) $installment->principal_amount, 2));

            LedgerEntry::create([
                'entity_type' => 'investment',
                'entity_id' => $investment->id,
                'entry_date' => $paidDate,
                'type' => 'payment',
                'amount' => $netPaidAmount,
                'principal_amount' => (float) $installment->principal_amount,
                'interest_amount' => (float) $installment->rent,
                'balance_after' => $newBalance,
                'description' => $this->paymentDescription($installment, $fine, $daysLate, $discountAmount, $netPaidAmount),
                'created_by' => Auth::id(),
            ]);

            $this->syncAccountAfterPayment($account, $installment, $netPaidAmount, $newBalance);
            $this->syncInvestmentAfterPayment($investment, $newBalance);

            return [
                'installment' => $installment->fresh()->load('paymentMethod'),
                'receipt_number' => $receiptNumber,
                'fine' => $fine,
                'days_late' => $daysLate,
                'net_paid' => $netPaidAmount,
            ];
        });
    }

    /**
     * Reverse a paid collection.
     */
    public function reverse(InvestmentInstallment $installment): void
    {
        DB::transaction(function () use ($installment) {
            $installment = InvestmentInstallment::whereKey($installment->id)->lockForUpdate()->firstOrFail();

            if ($installment->status !== 'paid') {
                throw new RuntimeException('Only paid installments can be reversed.');
            }

            // Block reverse if later installments are already paid
            $laterPaid = InvestmentInstallment::where('investment_id', $installment->investment_id)
                ->where('installment_number', '>', $installment->installment_number)
                ->where('status', 'paid')
                ->exists();

            if ($laterPaid) {
                throw new RuntimeException('Reverse later installments first before reversing this collection.');
            }

            $investment = Investment::whereKey($installment->investment_id)->lockForUpdate()->firstOrFail();
            $account = InvestmentAccount::where('investment_id', $investment->id)->lockForUpdate()->first();

            $ledgerEntry = LedgerEntry::where('entity_type', 'investment')
                ->where('entity_id', $investment->id)
                ->where('type', 'payment')
                ->where('description', 'like', '%installment #'.$installment->installment_number.'%')
                ->orderByDesc('entry_date')
                ->orderByDesc('id')
                ->first();

            $principal = (float) $installment->principal_amount;
            $rent = (float) $installment->rent;
            $paidAmount = (float) ($ledgerEntry?->amount ?? ((float) $installment->total_amount - (float) ($installment->discount_amount ?? 0)));
            $deletedLedgerId = $ledgerEntry?->id;

            if ($ledgerEntry) {
                $ledgerEntry->delete();
            }

            if ($deletedLedgerId) {
                LedgerEntry::where('entity_type', 'investment')
                    ->where('entity_id', $investment->id)
                    ->where('type', 'payment')
                    ->where('id', '>', $deletedLedgerId)
                    ->increment('balance_after', $principal);
            }

            $previousRemaining = $this->currentOutstandingPrincipal($investment);
            $restored = round($previousRemaining + $principal, 2);

            $installment->update([
                'status' => 'pending',
                'paid_date' => null,
                'fine_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => round($principal + $rent, 2),
                'paid_by' => null,
                'payment_method_id' => null,
                'transaction_reference' => null,
                'receipt_number' => null,
                'bank_name' => null,
                'check_number' => null,
                'updated_by' => Auth::id(),
            ]);

            if ($account) {
                $account->total_principal_paid = max(0, (float) $account->total_principal_paid - $principal);
                $account->total_rent_received = max(0, (float) $account->total_rent_received - $rent);
                $account->total_payments_made = max(0, (float) $account->total_payments_made - $paidAmount);
                $account->total_installments_paid = max(0, (int) $account->total_installments_paid - 1);
                $account->installments_paid_count = max(0, (int) $account->installments_paid_count - 1);
                $account->installments_pending_count = (int) $account->installments_pending_count + 1;
                $account->current_balance = $restored;
                $account->account_status = 'active';
                $account->updated_by = Auth::id();
                $account->save();
            }

            $investment->remaining_principal = $restored;
            $investment->ownership_ratio = $this->ownershipRatio($investment, $restored);
            if ($investment->status === 'matured') {
                $investment->status = 'active';
            }
            $investment->save();
        });
    }

    private function assertSequentialCollection(int $investmentId, int $installmentNumber): void
    {
        $blocked = InvestmentInstallment::where('investment_id', $investmentId)
            ->where('installment_number', '<', $installmentNumber)
            ->whereIn('status', ['pending', 'overdue'])
            ->exists();

        if ($blocked) {
            throw new RuntimeException('Earlier installments must be collected before this installment.');
        }
    }

    private function currentOutstandingPrincipal(Investment $investment): float
    {
        if ($investment->remaining_principal !== null) {
            return (float) $investment->remaining_principal;
        }

        $lastPaid = $investment->installments()
            ->where('status', 'paid')
            ->orderByDesc('installment_number')
            ->first();

        if ($lastPaid) {
            return (float) $lastPaid->ending_balance;
        }

        return (float) $investment->principal_amount;
    }

    private function syncAccountAfterPayment(
        InvestmentAccount $account,
        InvestmentInstallment $installment,
        float $netPaid,
        float $newBalance
    ): void {
        $account->total_principal_paid = (float) $account->total_principal_paid + (float) $installment->principal_amount;
        $account->total_rent_received = (float) $account->total_rent_received + (float) $installment->rent;
        $account->total_interest_received = (float) $account->total_interest_received + (float) $installment->rent;
        $account->total_payments_made = (float) $account->total_payments_made + $netPaid;
        $account->total_installments_paid = (int) $account->total_installments_paid + 1;
        $account->installments_paid_count = (int) $account->installments_paid_count + 1;
        $account->installments_pending_count = max(0, (int) $account->installments_pending_count - 1);
        $account->current_balance = $newBalance;
        $account->updated_by = Auth::id();

        if ($newBalance <= 0) {
            $account->account_status = 'matured';
        }

        $account->save();
    }

    private function syncInvestmentAfterPayment(Investment $investment, float $newBalance): void
    {
        $investment->remaining_principal = $newBalance;
        $investment->ownership_ratio = $this->ownershipRatio($investment, $newBalance);

        if ($newBalance <= 0) {
            $investment->status = 'matured';
        }

        $investment->save();
    }

    private function ownershipRatio(Investment $investment, float $remainingPrincipal): ?float
    {
        $principal = (float) $investment->principal_amount;
        if ($principal <= 0) {
            return null;
        }

        // Customer ownership share of the asset (HPSM); Bai-Muajjal leaves null-friendly 0–1 paid ratio
        return round(($principal - $remainingPrincipal) / $principal, 4);
    }

    private function paymentDescription(
        InvestmentInstallment $installment,
        float $fine,
        int $daysLate,
        float $discountAmount,
        float $netPaidAmount
    ): string {
        $desc = "Payment for installment #{$installment->installment_number}";
        if ($fine > 0) {
            $desc .= ' (Fine: ৳'.number_format($fine, 2)." for {$daysLate} days late)";
        }
        if ($discountAmount > 0) {
            $desc .= ' (Discount: ৳'.number_format($discountAmount, 2).', Net: ৳'.number_format($netPaidAmount, 2).')';
        }

        return $desc;
    }

    private function methodLabel(?string $method): ?string
    {
        return match ($method) {
            'annuity' => 'Annuity',
            'reducing' => 'Reducing Balance',
            default => $method,
        };
    }

    private function generateReceiptNumber(): string
    {
        do {
            $receipt = 'RCP-'.date('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (InvestmentInstallment::where('receipt_number', $receipt)->exists());

        return $receipt;
    }
}
