<?php

namespace App\Services\Investment;

use App\DTO\Investment\InvestmentCalculationResult;
use App\Models\Investment;
use App\Models\InvestmentAccount;
use App\Models\InvestmentInstallment;
use App\Models\InvestmentType;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Orchestrates Islamic investment creation: calculate, persist, schedule, ledger, account.
 *
 * Controllers must not contain business logic — call this service instead.
 */
class InvestmentService
{
    public function __construct(
        private readonly InvestmentCalculatorFactory $calculatorFactory
    ) {
    }

    /**
     * Preview calculation for AJAX (does not persist).
     *
     * @param  array<string, mixed>  $input
     */
    public function calculate(array $input): InvestmentCalculationResult
    {
        $calculator = $this->calculatorFactory->make(
            $input['investment_type_id'] ?? null,
            $input['calculation_method'] ?? null,
            $input['product_code'] ?? null
        );

        return $calculator->calculate([
            'principal_amount' => $input['principal_amount'],
            'annual_rate' => $input['interest_rate'] ?? $input['annual_rate'] ?? 0,
            'investment_years' => $input['investment_years'],
            'start_date' => $input['start_date'],
            'payment_type' => $input['payment_type'] ?? 'monthly',
        ]);
    }

    /**
     * Create investment using server-side recalculation only (never trust browser totals).
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): Investment
    {
        return DB::transaction(function () use ($input) {
            $type = InvestmentType::query()->findOrFail($input['investment_type_id']);
            $code = strtolower((string) ($type->code ?? ''));

            if ($code === 'hpsm' && empty($input['calculation_method'])) {
                throw new InvalidArgumentException('Calculation method is required for HPSM.');
            }

            if ($code === 'bai_muajjal') {
                $input['calculation_method'] = null;
            }

            $result = $this->calculate($input);
            $summary = $result->summary;
            $schedule = $result->schedule;

            $annualPercent = (float) $summary['annual_rate_percent'];
            // Persist rate as fraction for backward compatibility with getRatePercentageAttribute
            $rateFraction = $annualPercent / 100;

            $investment = Investment::create([
                'member_id' => $input['member_id'],
                'investment_type_id' => $type->id,
                'principal_amount' => $summary['principal_amount'],
                'selling_price' => $summary['selling_price'],
                'profit_amount' => $summary['total_profit'],
                'emi_amount' => $summary['monthly_installment'],
                'remaining_principal' => $summary['remaining_principal'],
                'ownership_ratio' => $summary['ownership_ratio'],
                'product_name' => $type->investment_type_name,
                'calculation_method' => $summary['calculation_method'],
                'start_date' => $input['start_date'],
                'account_opening_date' => $input['account_opening_date'] ?? $input['start_date'],
                'gestation_date' => $input['gestation_date'] ?? $input['gestation_maturity_date'] ?? null,
                'term_months' => $summary['number_of_installments'],
                'expiry_date' => $summary['maturity_date'],
                'rate' => $rateFraction,
                'rate_period' => 'annual',
                'frequency' => 'monthly',
                'status' => 'active',
                'notes' => $input['notes'] ?? null,
            ]);

            $this->createPrincipalLedger($investment, $input);
            $this->createProfitLedger($investment, $summary);
            $this->persistSchedule($investment, $schedule);
            $this->createAccount($investment, $input, $summary);

            return $investment->load(['member', 'account', 'installments', 'investmentType']);
        });
    }

    /**
     * @param  array<string, mixed>  $input
     */
    private function createPrincipalLedger(Investment $investment, array $input): void
    {
        LedgerEntry::create([
            'entity_type' => 'investment',
            'entity_id' => $investment->id,
            'entry_date' => $input['start_date'],
            'type' => 'principal',
            'amount' => $investment->principal_amount,
            'principal_amount' => $investment->principal_amount,
            'interest_amount' => 0,
            'balance_after' => $investment->principal_amount,
            'description' => 'Initial investment principal ('.$investment->product_name.')',
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $summary
     */
    private function createProfitLedger(Investment $investment, array $summary): void
    {
        LedgerEntry::create([
            'entity_type' => 'investment',
            'entity_id' => $investment->id,
            'entry_date' => $investment->start_date?->toDateString() ?? now()->toDateString(),
            'type' => 'interest',
            'amount' => $summary['total_profit'],
            'principal_amount' => 0,
            'interest_amount' => $summary['total_profit'],
            'balance_after' => $investment->principal_amount,
            'description' => 'Recognized profit/rent on investment opening ('.$investment->product_name.')',
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * @param  list<array<string, mixed>>  $schedule
     */
    private function persistSchedule(Investment $investment, array $schedule): void
    {
        $rows = [];
        $now = now();
        $userId = Auth::id();

        foreach ($schedule as $row) {
            $rows[] = [
                'investment_id' => $investment->id,
                'installment_number' => $row['installment_number'],
                'schedule_date' => $row['schedule_date'],
                'beginning_balance' => $row['beginning_balance'],
                'principal_amount' => $row['principal_amount'],
                'rent' => $row['rent'],
                'total_amount' => $row['total_amount'],
                'ending_balance' => $row['ending_balance'],
                'cumulative_rent' => $row['cumulative_rent'],
                'fine_amount' => 0,
                'discount_amount' => 0,
                'status' => 'pending',
                'created_by' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        InvestmentInstallment::insert($rows);
    }

    /**
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>  $summary
     */
    private function createAccount(Investment $investment, array $input, array $summary): void
    {
        $account = InvestmentAccount::create([
            'investment_id' => $investment->id,
            'account_opening_date' => $input['account_opening_date'] ?? $input['start_date'],
            'account_closing_date' => $input['gestation_date'] ?? $input['gestation_maturity_date'] ?? $summary['maturity_date'],
            'opening_balance' => $summary['principal_amount'],
            'current_balance' => $summary['principal_amount'],
            'total_principal_paid' => 0,
            'total_interest_received' => 0,
            'total_rent_received' => 0,
            'total_payments_made' => 0,
            'total_installments_paid' => 0,
            'installments_paid_count' => 0,
            'installments_pending_count' => $summary['number_of_installments'],
            'installments_overdue_count' => 0,
            'account_status' => 'active',
            'account_notes' => $input['notes'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $account->generateAccountNumber();
    }
}
