<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\DepositType;
use App\Models\Member;
use App\Models\LedgerEntry;
use App\Models\Import;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;

class DepositImportService
{
    /**
     * Import deposits and ledger entries from Excel file
     */
    public function importFromFile($filePath, Import $import)
    {
        $errors = [];
        $rowsImported = 0;
        $depositsCreated = 0;
        $ledgerEntriesCreated = 0;

        try {
            $spreadsheet = IOFactory::load(Storage::path($filePath));
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            // Get headers
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headers[$col] = $worksheet->getCell($col . '1')->getValue();
            }

            // Process each row
            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $rowData = [];
                    for ($col = 'A'; $col <= $highestColumn; $col++) {
                        $rowData[$headers[$col]] = $worksheet->getCell($col . $row)->getValue();
                    }

                    $result = $this->processRow($rowData, $row);
                    
                    if ($result['success']) {
                        $rowsImported++;
                        if ($result['deposit_created']) {
                            $depositsCreated++;
                        }
                        if ($result['ledger_entries_created']) {
                            $ledgerEntriesCreated += $result['ledger_entries_created'];
                        }
                    } else {
                        $errors[] = "Row {$row}: " . $result['error'];
                    }

                } catch (\Exception $e) {
                    $errors[] = "Row {$row}: " . $e->getMessage();
                }
            }

            // Update import record
            $import->update([
                'rows_imported' => $rowsImported,
                'errors' => $errors
            ]);

            return [
                'rows_imported' => $rowsImported,
                'deposits_created' => $depositsCreated,
                'ledger_entries_created' => $ledgerEntriesCreated,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            $import->update([
                'errors' => array_merge($errors, ['General error: ' . $e->getMessage()])
            ]);
            
            throw $e;
        }
    }

    /**
     * Process a single row of data
     */
    private function processRow($rowData, $rowNumber)
    {
        try {
            // Extract data from row
            $memberId = $this->findMemberId($rowData);
            if (!$memberId) {
                return ['success' => false, 'error' => 'Member not found'];
            }

            $depositAmount = $this->parseAmount($rowData['Deposit'] ?? $rowData['Amount'] ?? 0);
            $startDate = $this->parseDate($rowData['Date'] ?? $rowData['Start Date']);
            $productName = $rowData['Product'] ?? $rowData['Product Name'] ?? null;
            $depositType = $this->parseDepositType($rowData['Type'] ?? 'savings');
            $rate = $this->parseRate($rowData['Rate'] ?? $rowData['Interest Rate'] ?? null);
            $maturityDate = $this->parseDate($rowData['Maturity Date'] ?? null);

            // Create or find deposit
            $deposit = $this->createOrFindDeposit([
                'member_id' => $memberId,
                'deposit_amount' => $depositAmount,
                'product_name' => $productName,
                'start_date' => $startDate,
                'maturity_date' => $maturityDate,
                'rate' => $rate,
                'deposit_type_id' => $this->getDepositTypeId($depositType),
                'status' => 'active'
            ]);

            $depositCreated = $deposit->wasRecentlyCreated;
            $ledgerEntriesCreated = 0;

            // Create ledger entries
            if (isset($rowData['Deposit']) && $rowData['Deposit'] > 0) {
                $this->createLedgerEntry($deposit, 'deposit', $depositAmount, $startDate, 'Initial deposit from import');
                $ledgerEntriesCreated++;
            }

            if (isset($rowData['Withdrawal']) && $rowData['Withdrawal'] > 0) {
                $withdrawalAmount = $this->parseAmount($rowData['Withdrawal']);
                $this->createLedgerEntry($deposit, 'withdrawal', $withdrawalAmount, $startDate, 'Withdrawal from import');
                $ledgerEntriesCreated++;
            }

            if (isset($rowData['Interest']) && $rowData['Interest'] > 0) {
                $interestAmount = $this->parseAmount($rowData['Interest']);
                $this->createLedgerEntry($deposit, 'accrual', $interestAmount, $startDate, 'Interest from import');
                $ledgerEntriesCreated++;
            }

            return [
                'success' => true,
                'deposit_created' => $depositCreated,
                'ledger_entries_created' => $ledgerEntriesCreated
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Find member ID from row data
     */
    private function findMemberId($rowData)
    {
        // Try different possible member identifier columns
        $possibleColumns = ['Member ID', 'MemberId', 'Member', 'Name', 'Member Name'];
        
        foreach ($possibleColumns as $column) {
            if (isset($rowData[$column])) {
                $value = $rowData[$column];
                
                // If it's a number, treat as ID
                if (is_numeric($value)) {
                    $member = Member::find($value);
                    if ($member) {
                        return $member->id;
                    }
                }
                
                // If it's a string, search by name or unique ID
                $member = Member::where('name', 'like', "%{$value}%")
                    ->orWhere('member_unique_id', $value)
                    ->first();
                    
                if ($member) {
                    return $member->id;
                }
            }
        }
        
        return null;
    }

    /**
     * Create or find deposit
     */
    private function createOrFindDeposit($data)
    {
        // Try to find existing deposit
        $deposit = Deposit::where('member_id', $data['member_id'])
            ->where('start_date', $data['start_date'])
            ->where('deposit_amount', $data['deposit_amount'])
            ->first();

        if (!$deposit) {
            $deposit = Deposit::create($data);
        }

        return $deposit;
    }

    /**
     * Create ledger entry
     */
    private function createLedgerEntry($deposit, $type, $amount, $date, $description)
    {
        $currentBalance = $deposit->current_balance;
        
        if ($type === 'deposit' || $type === 'accrual') {
            $newBalance = $currentBalance + $amount;
        } elseif ($type === 'withdrawal') {
            $newBalance = $currentBalance - $amount;
        } else {
            $newBalance = $currentBalance + $amount;
        }

        LedgerEntry::create([
            'entity_type' => 'deposit',
            'entity_id' => $deposit->id,
            'entry_date' => $date,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $newBalance,
            'description' => $description,
            'created_by' => auth()->id() ?? 1 // Fallback to admin user
        ]);
    }

    /**
     * Parse amount from various formats
     */
    private function parseAmount($value)
    {
        if (is_numeric($value)) {
            return (float) $value;
        }
        
        // Remove currency symbols and commas
        $value = preg_replace('/[^\d.-]/', '', $value);
        return (float) $value;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            // Handle Excel serial dates
            if (is_numeric($value)) {
                return Carbon::createFromFormat('Y-m-d', '1900-01-01')->addDays($value - 2);
            }
            
            // Handle string dates
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return Carbon::now()->format('Y-m-d');
        }
    }

    /**
     * Parse deposit type
     */
    private function parseDepositType($value)
    {
        $value = strtolower(trim($value));
        
        if (in_array($value, ['savings', 'saving'])) {
            return 'savings';
        } elseif (in_array($value, ['fixed', 'fd', 'fixed deposit'])) {
            return 'fixed';
        } elseif (in_array($value, ['recurring', 'rd', 'recurring deposit'])) {
            return 'recurring';
        }
        
        return 'savings'; // Default
    }

    /**
     * Parse interest rate
     */
    private function parseRate($value)
    {
        if (!$value || !is_numeric($value)) {
            return null;
        }
        
        $rate = (float) $value;
        
        // If rate is greater than 1, assume it's a percentage
        if ($rate > 1) {
            $rate = $rate / 100;
        }
        
        return $rate;
    }

    /**
     * Generate import template
     */
    public function generateTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'A1' => 'Member ID',
            'B1' => 'Member Name',
            'C1' => 'Deposit Amount',
            'D1' => 'Product Name',
            'E1' => 'Start Date',
            'F1' => 'Maturity Date',
            'G1' => 'Deposit Type',
            'H1' => 'Interest Rate',
            'I1' => 'Withdrawal',
            'J1' => 'Interest',
            'K1' => 'Balance'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Sample data
        $sampleData = [
            ['A2' => '1', 'B2' => 'John Doe', 'C2' => '10000', 'D2' => 'Savings Account', 'E2' => '2023-01-01', 'F2' => '2024-01-01', 'G2' => 'savings', 'H2' => '0.05', 'I2' => '0', 'J2' => '500', 'K2' => '10500'],
            ['A3' => '2', 'B3' => 'Jane Smith', 'C3' => '50000', 'D3' => 'Fixed Deposit', 'E3' => '2023-06-01', 'F3' => '2024-06-01', 'G3' => 'fixed', 'H3' => '0.08', 'I3' => '0', 'J3' => '2000', 'K3' => '52000']
        ];

        foreach ($sampleData as $row) {
            foreach ($row as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }
        }

        // Auto-size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'deposit_import_template_' . time() . '.xlsx';
        $filePath = storage_path('app/templates/' . $filename);
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }
}
