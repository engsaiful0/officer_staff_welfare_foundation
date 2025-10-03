<?php

namespace App\Console\Commands;

use App\Models\Investment;
use App\Models\LedgerEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProcessInvestmentAccruals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investments:process-accruals 
                            {--date= : Specific date to process accruals for (YYYY-MM-DD)}
                            {--investment= : Specific investment ID to process}
                            {--dry-run : Show what would be processed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process interest accruals for active investments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::now();
        $investmentId = $this->option('investment');
        $dryRun = $this->option('dry-run');

        $this->info("Processing investment accruals for date: {$date->format('Y-m-d')}");
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        try {
            $query = Investment::where('status', 'active')
                ->where('start_date', '<=', $date)
                ->where('expiry_date', '>', $date);

            if ($investmentId) {
                $query->where('id', $investmentId);
            }

            $investments = $query->get();

            if ($investments->isEmpty()) {
                $this->info('No active investments found for processing.');
                return 0;
            }

            $this->info("Found {$investments->count()} investments to process.");

            $processed = 0;
            $skipped = 0;
            $errors = 0;

            foreach ($investments as $investment) {
                try {
                    $result = $this->processInvestmentAccrual($investment, $date, $dryRun);
                    
                    if ($result['processed']) {
                        $processed++;
                        $this->line("✓ Investment #{$investment->id} - {$investment->member->name}: {$result['message']}");
                    } else {
                        $skipped++;
                        $this->line("- Investment #{$investment->id} - {$investment->member->name}: {$result['message']}");
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("✗ Investment #{$investment->id} - {$investment->member->name}: {$e->getMessage()}");
                }
            }

            $this->newLine();
            $this->info("Processing Summary:");
            $this->info("  Processed: {$processed}");
            $this->info("  Skipped: {$skipped}");
            $this->info("  Errors: {$errors}");

            return 0;

        } catch (\Exception $e) {
            $this->error("Command failed: {$e->getMessage()}");
            return 1;
        }
    }

    /**
     * Process accrual for a single investment
     */
    private function processInvestmentAccrual(Investment $investment, Carbon $date, bool $dryRun = false)
    {
        // Check if accrual is needed based on frequency
        if (!$this->shouldProcessAccrual($investment, $date)) {
            return [
                'processed' => false,
                'message' => 'No accrual needed for this period'
            ];
        }

        // Calculate interest amount
        $interestAmount = $this->calculateInterest($investment, $date);
        
        if ($interestAmount <= 0) {
            return [
                'processed' => false,
                'message' => 'No interest to accrue'
            ];
        }

        if ($dryRun) {
            return [
                'processed' => true,
                'message' => "Would accrue {$interestAmount} interest"
            ];
        }

        // Create the accrual entry
        DB::beginTransaction();
        
        try {
            $currentBalance = $investment->current_balance;
            $newBalance = $currentBalance + $interestAmount;

            LedgerEntry::create([
                'investment_id' => $investment->id,
                'entry_date' => $date->format('Y-m-d'),
                'type' => 'accrual',
                'amount' => $interestAmount,
                'interest_amount' => $interestAmount,
                'balance_after' => $newBalance,
                'description' => 'Automatic interest accrual',
                'created_by' => 1 // System user
            ]);

            DB::commit();

            return [
                'processed' => true,
                'message' => "Accrued {$interestAmount} interest"
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Check if accrual should be processed based on frequency
     */
    private function shouldProcessAccrual(Investment $investment, Carbon $date)
    {
        $lastAccrual = LedgerEntry::where('investment_id', $investment->id)
            ->where('type', 'accrual')
            ->orderBy('entry_date', 'desc')
            ->first();

        if (!$lastAccrual) {
            // First accrual - check if enough time has passed since start date
            $daysSinceStart = Carbon::parse($investment->start_date)->diffInDays($date);
            return $daysSinceStart >= 1; // At least 1 day
        }

        $lastAccrualDate = Carbon::parse($lastAccrual->entry_date);
        $daysSinceLastAccrual = $lastAccrualDate->diffInDays($date);

        switch ($investment->frequency) {
            case 'daily':
                return $daysSinceLastAccrual >= 1;
            case 'monthly':
                return $daysSinceLastAccrual >= 30;
            case 'quarterly':
                return $daysSinceLastAccrual >= 90;
            default:
                return $daysSinceLastAccrual >= 30; // Default to monthly
        }
    }

    /**
     * Calculate interest amount for an investment
     */
    private function calculateInterest(Investment $investment, Carbon $date)
    {
        $currentBalance = $investment->current_balance;
        $rate = $investment->rate;
        
        // Get the last accrual date
        $lastAccrual = LedgerEntry::where('investment_id', $investment->id)
            ->where('type', 'accrual')
            ->orderBy('entry_date', 'desc')
            ->first();

        $lastAccrualDate = $lastAccrual ? Carbon::parse($lastAccrual->entry_date) : Carbon::parse($investment->start_date);
        $daysSinceLastAccrual = $lastAccrualDate->diffInDays($date);

        if ($daysSinceLastAccrual <= 0) {
            return 0;
        }

        // Calculate interest based on rate period
        if ($investment->rate_period === 'annual') {
            $dailyRate = $rate / 365;
        } else { // monthly
            $dailyRate = $rate / 30;
        }

        return $currentBalance * $dailyRate * $daysSinceLastAccrual;
    }
}
