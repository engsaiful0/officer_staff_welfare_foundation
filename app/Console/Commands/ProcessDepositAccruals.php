<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\LedgerEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessDepositAccruals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposits:process-accruals 
                            {--date= : Process accruals for a specific date (Y-m-d)}
                            {--deposit= : Process accruals for a specific deposit ID}
                            {--dry-run : Show what would be processed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process interest accruals for deposits with interest rates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        $depositId = $this->option('deposit');
        $dryRun = $this->option('dry-run');

        $this->info("Processing deposit accruals for date: {$date->format('Y-m-d')}");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        try {
            $query = Deposit::withInterest()
                ->active()
                ->where('start_date', '<=', $date);

            if ($depositId) {
                $query->where('id', $depositId);
            }

            $deposits = $query->get();

            if ($deposits->isEmpty()) {
                $this->info('No deposits found for accrual processing.');
                return 0;
            }

            $this->info("Found {$deposits->count()} deposits for processing.");

            $processedCount = 0;
            $errorCount = 0;

            foreach ($deposits as $deposit) {
                try {
                    $result = $this->processDepositAccrual($deposit, $date, $dryRun);
                    
                    if ($result['success']) {
                        $processedCount++;
                        $this->line("✓ Processed deposit #{$deposit->id} - {$deposit->member->name} - Amount: {$result['amount']}");
                    } else {
                        $errorCount++;
                        $this->error("✗ Failed to process deposit #{$deposit->id}: {$result['error']}");
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->error("✗ Error processing deposit #{$deposit->id}: {$e->getMessage()}");
                }
            }

            $this->info("\nProcessing completed:");
            $this->info("✓ Successfully processed: {$processedCount}");
            if ($errorCount > 0) {
                $this->error("✗ Errors: {$errorCount}");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Fatal error: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Process accrual for a single deposit
     */
    private function processDepositAccrual(Deposit $deposit, Carbon $date, bool $dryRun = false)
    {
        try {
            // Check if accrual already exists for this date
            $existingAccrual = $deposit->ledgerEntries()
                ->where('type', 'accrual')
                ->whereDate('entry_date', $date)
                ->first();

            if ($existingAccrual) {
                return [
                    'success' => false,
                    'error' => 'Accrual already exists for this date'
                ];
            }

            // Calculate interest based on deposit type and rate
            $interestAmount = $this->calculateInterest($deposit, $date);

            if ($interestAmount <= 0) {
                return [
                    'success' => false,
                    'error' => 'No interest to accrue'
                ];
            }

            if (!$dryRun) {
                DB::beginTransaction();

                $currentBalance = $deposit->current_balance;
                $newBalance = $currentBalance + $interestAmount;

                LedgerEntry::create([
                    'entity_type' => 'deposit',
                    'entity_id' => $deposit->id,
                    'entry_date' => $date,
                    'type' => 'accrual',
                    'amount' => $interestAmount,
                    'balance_after' => $newBalance,
                    'description' => 'Interest accrual - ' . $deposit->rate_percentage . '%',
                    'created_by' => 1 // System user
                ]);

                DB::commit();
            }

            return [
                'success' => true,
                'amount' => number_format($interestAmount, 2)
            ];

        } catch (\Exception $e) {
            if (!$dryRun) {
                DB::rollBack();
            }
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate interest for a deposit
     */
    private function calculateInterest(Deposit $deposit, Carbon $date)
    {
        $currentBalance = $deposit->current_balance;
        
        if ($currentBalance <= 0) {
            return 0;
        }

        // Calculate days since last accrual or start date
        $lastAccrual = $deposit->ledgerEntries()
            ->where('type', 'accrual')
            ->orderBy('entry_date', 'desc')
            ->first();

        $startDate = $lastAccrual ? $lastAccrual->entry_date : $deposit->start_date;
        $days = $date->diffInDays($startDate);

        if ($days <= 0) {
            return 0;
        }

        // Calculate interest based on deposit type
        switch ($deposit->deposit_type) {
            case 'savings':
                // Daily interest for savings
                $dailyRate = $deposit->rate / 365;
                return $currentBalance * $dailyRate * $days;

            case 'fixed':
                // Monthly interest for fixed deposits
                $months = $days / 30;
                $monthlyRate = $deposit->rate / 12;
                return $currentBalance * $monthlyRate * $months;

            case 'recurring':
                // Monthly interest for recurring deposits
                $months = $days / 30;
                $monthlyRate = $deposit->rate / 12;
                return $currentBalance * $monthlyRate * $months;

            default:
                return 0;
        }
    }
}
