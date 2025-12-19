<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\LedgerEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessMonthlyDeposits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deposits:process-monthly {--date=} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process monthly deposits for all active deposit accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        $dryRun = $this->option('dry-run');

        $this->info("Processing monthly deposits for date: {$date->format('Y-m-d')}");

        // Get all active deposits with monthly deposit amounts
        $deposits = Deposit::withMonthlyDeposits()
            ->where('status', 'active')
            ->get();

        $this->info("Found {$deposits->count()} deposits with monthly deposit configuration");

        $processed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($deposits as $deposit) {
            try {
                if ($deposit->isMonthlyDepositDue($date)) {
                    if ($dryRun) {
                        $this->line("  [DRY RUN] Would process monthly deposit for Deposit #{$deposit->id} (Account: {$deposit->account_number}) - Amount: {$deposit->monthly_deposit_amount}");
                        $processed++;
                    } else {
                        $result = $this->processMonthlyDeposit($deposit, $date);
                        if ($result['success']) {
                            $this->info("  ✓ Processed monthly deposit for Deposit #{$deposit->id} (Account: {$deposit->account_number}) - Amount: {$result['amount']}");
                            $processed++;
                        } else {
                            $this->error("  ✗ Failed to process Deposit #{$deposit->id}: {$result['error']}");
                            $errors++;
                        }
                    }
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Error processing Deposit #{$deposit->id}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  Processed: {$processed}");
        $this->line("  Skipped: {$skipped}");
        $this->line("  Errors: {$errors}");

        return Command::SUCCESS;
    }

    /**
     * Process monthly deposit for a single deposit account
     */
    private function processMonthlyDeposit(Deposit $deposit, Carbon $date)
    {
        try {
            DB::beginTransaction();

            $monthlyAmount = $deposit->monthly_deposit_amount;
            $currentBalance = $deposit->current_balance;
            $newBalance = $currentBalance + $monthlyAmount;

            // Create ledger entry for monthly deposit
            LedgerEntry::create([
                'entity_type' => 'deposit',
                'entity_id' => $deposit->id,
                'entry_date' => $date->format('Y-m-d'),
                'type' => 'deposit',
                'amount' => $monthlyAmount,
                'balance_after' => $newBalance,
                'description' => 'Monthly deposit - ' . $date->format('F Y'),
                'created_by' => 1 // System user
            ]);

            // Update last deposit date
            $deposit->update([
                'last_deposit_date' => $date->format('Y-m-d')
            ]);

            DB::commit();

            return [
                'success' => true,
                'amount' => number_format($monthlyAmount, 2)
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
