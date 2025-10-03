<?php

namespace App\Services;

use App\Models\Investment;
use App\Models\LedgerEntry;
use App\Models\Member;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class InvestmentImportService
{
    /**
     * Import investments from Excel
     */
    public function importInvestments($filePath, $importId)
    {
        $errors = [];
        $imported = 0;

        try {
            $data = Excel::toArray([], storage_path('app/' . $filePath));
            
            if (empty($data) || empty($data[0])) {
                throw new \Exception('No data found in the Excel file');
            }

            $rows = $data[0];
            $header = array_shift($rows); // Remove header row

            foreach ($rows as $index => $row) {
                try {
                    $rowNumber = $index + 2; // +2 because we removed header and arrays are 0-indexed
                    
                    // Expected columns: Member ID, Principal Amount, Product Name, Start Date, Term Months, Rate, Rate Period, Frequency, Notes
                    if (count($row) < 6) {
                        $errors[] = "Row {$rowNumber}: Insufficient columns";
                        continue;
                    }

                    $memberId = $row[0];
                    $principalAmount = $row[1];
                    $productName = $row[2] ?? null;
                    $startDate = $row[3];
                    $termMonths = $row[4];
                    $rate = $row[5];
                    $ratePeriod = $row[6] ?? 'annual';
                    $frequency = $row[7] ?? 'monthly';
                    $notes = $row[8] ?? null;

                    // Validate member exists
                    $member = Member::find($memberId);
                    if (!$member) {
                        $errors[] = "Row {$rowNumber}: Member ID {$memberId} not found";
                        continue;
                    }

                    // Validate and convert data
                    $startDate = $this->parseDate($startDate);
                    if (!$startDate) {
                        $errors[] = "Row {$rowNumber}: Invalid start date format";
                        continue;
                    }

                    $expiryDate = Carbon::parse($startDate)->addMonths($termMonths);

                    // Convert rate to decimal if it's a percentage
                    if ($rate > 1) {
                        $rate = $rate / 100;
                    }

                    $investment = Investment::create([
                        'member_id' => $memberId,
                        'principal_amount' => $principalAmount,
                        'product_name' => $productName,
                        'start_date' => $startDate,
                        'term_months' => $termMonths,
                        'expiry_date' => $expiryDate,
                        'rate' => $rate,
                        'rate_period' => $ratePeriod,
                        'frequency' => $frequency,
                        'status' => 'active',
                        'notes' => $notes
                    ]);

                    // Create initial ledger entry
                    LedgerEntry::create([
                        'investment_id' => $investment->id,
                        'entry_date' => $startDate,
                        'type' => 'principal',
                        'amount' => $principalAmount,
                        'principal_amount' => $principalAmount,
                        'balance_after' => $principalAmount,
                        'description' => 'Initial investment principal (imported)',
                        'created_by' => auth()->id()
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

        } catch (\Exception $e) {
            $errors[] = "File processing error: " . $e->getMessage();
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * Import ledger entries from Excel
     */
    public function importLedgerEntries($filePath, $importId)
    {
        $errors = [];
        $imported = 0;

        try {
            $data = Excel::toArray([], storage_path('app/' . $filePath));
            
            if (empty($data) || empty($data[0])) {
                throw new \Exception('No data found in the Excel file');
            }

            $rows = $data[0];
            $header = array_shift($rows); // Remove header row

            foreach ($rows as $index => $row) {
                try {
                    $rowNumber = $index + 2;
                    
                    // Expected columns: Investment ID, Entry Date, Type, Amount, Interest Amount, Principal Amount, Description
                    if (count($row) < 4) {
                        $errors[] = "Row {$rowNumber}: Insufficient columns";
                        continue;
                    }

                    $investmentId = $row[0];
                    $entryDate = $row[1];
                    $type = $row[2];
                    $amount = $row[3];
                    $interestAmount = $row[4] ?? null;
                    $principalAmount = $row[5] ?? null;
                    $description = $row[6] ?? null;

                    // Validate investment exists
                    $investment = Investment::find($investmentId);
                    if (!$investment) {
                        $errors[] = "Row {$rowNumber}: Investment ID {$investmentId} not found";
                        continue;
                    }

                    // Validate entry type
                    if (!in_array($type, ['accrual', 'payment', 'credit', 'adjustment'])) {
                        $errors[] = "Row {$rowNumber}: Invalid entry type '{$type}'";
                        continue;
                    }

                    // Parse date
                    $entryDate = $this->parseDate($entryDate);
                    if (!$entryDate) {
                        $errors[] = "Row {$rowNumber}: Invalid entry date format";
                        continue;
                    }

                    // Calculate balance after
                    $currentBalance = $investment->current_balance;
                    $newBalance = $currentBalance;
                    
                    switch ($type) {
                        case 'payment':
                            $newBalance -= $amount;
                            break;
                        case 'credit':
                        case 'accrual':
                            $newBalance += $amount;
                            break;
                        case 'adjustment':
                            $newBalance += $amount;
                            break;
                    }

                    LedgerEntry::create([
                        'investment_id' => $investmentId,
                        'entry_date' => $entryDate,
                        'type' => $type,
                        'amount' => $amount,
                        'interest_amount' => $interestAmount,
                        'principal_amount' => $principalAmount,
                        'balance_after' => $newBalance,
                        'description' => $description ?: "Imported entry - {$type}",
                        'created_by' => auth()->id()
                    ]);

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }
            }

        } catch (\Exception $e) {
            $errors[] = "File processing error: " . $e->getMessage();
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (is_numeric($date)) {
            // Excel serial date
            return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($date - 2);
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
