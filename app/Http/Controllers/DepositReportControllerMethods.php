<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf;

trait DepositReportControllerMethods
{
    /**
     * Export portfolio report as PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Deposit::with(['member', 'depositType', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }]);

        // Apply same filters as portfolio report
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('deposit_type')) {
            $query->where('deposit_type', $request->deposit_type);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $deposits = $query->orderBy('created_at', 'desc')->get();

        $summary = [
            'total_deposits' => $deposits->count(),
            'total_amount' => $deposits->sum('deposit_amount'),
            'total_balance' => $deposits->sum('current_balance'),
            'total_interest_accrued' => $deposits->sum('total_interest_accrued'),
            'active_deposits' => $deposits->where('status', 'active')->count(),
            'matured_deposits' => $deposits->where('status', 'matured')->count(),
            'closed_deposits' => $deposits->where('status', 'closed')->count(),
        ];

        $pdf = Pdf::loadView('deposits.reports.pdf.portfolio', compact('deposits', 'summary'));
        
        return $pdf->download('deposit_portfolio_report_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export portfolio report as Excel
     */
    public function exportExcel(Request $request)
    {
        $query = Deposit::with(['member', 'depositType', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }]);

        // Apply same filters as portfolio report
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('deposit_type')) {
            $query->where('deposit_type', $request->deposit_type);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $deposits = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'A1' => 'Deposit ID',
            'B1' => 'Member Name',
            'C1' => 'Member ID',
            'D1' => 'Product Name',
            'E1' => 'Deposit Type',
            'F1' => 'Start Date',
            'G1' => 'Maturity Date',
            'H1' => 'Deposit Amount',
            'I1' => 'Current Balance',
            'J1' => 'Interest Rate (%)',
            'K1' => 'Status',
            'L1' => 'Interest Accrued',
            'M1' => 'Created Date'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        foreach ($deposits as $deposit) {
            $sheet->setCellValue('A' . $row, $deposit->id);
            $sheet->setCellValue('B' . $row, $deposit->member->name);
            $sheet->setCellValue('C' . $row, $deposit->member->member_unique_id);
            $sheet->setCellValue('D' . $row, $deposit->product_name);
            $sheet->setCellValue('E' . $row, $deposit->depositType ? $deposit->depositType->deposit_type_name : 'N/A');
            $sheet->setCellValue('F' . $row, $deposit->start_date->format('Y-m-d'));
            $sheet->setCellValue('G' . $row, $deposit->maturity_date ? $deposit->maturity_date->format('Y-m-d') : '');
            $sheet->setCellValue('H' . $row, $deposit->deposit_amount);
            $sheet->setCellValue('I' . $row, $deposit->current_balance);
            $sheet->setCellValue('J' . $row, $deposit->rate_percentage);
            $sheet->setCellValue('K' . $row, ucfirst($deposit->status));
            $sheet->setCellValue('L' . $row, $deposit->total_interest_accrued);
            $sheet->setCellValue('M' . $row, $deposit->created_at->format('Y-m-d H:i:s'));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to data
        $dataRange = 'A1:M' . ($row - 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Add summary section
        $summaryRow = $row + 2;
        $sheet->setCellValue('A' . $summaryRow, 'SUMMARY');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);
        
        $summaryData = [
            'Total Deposits' => $deposits->count(),
            'Total Deposit Amount' => $deposits->sum('deposit_amount'),
            'Total Current Balance' => $deposits->sum('current_balance'),
            'Total Interest Accrued' => $deposits->sum('total_interest_accrued'),
            'Active Deposits' => $deposits->where('status', 'active')->count(),
            'Matured Deposits' => $deposits->where('status', 'matured')->count(),
            'Closed Deposits' => $deposits->where('status', 'closed')->count(),
        ];

        $summaryRow++;
        foreach ($summaryData as $label => $value) {
            $sheet->setCellValue('A' . $summaryRow, $label);
            $sheet->setCellValue('B' . $summaryRow, $value);
            $summaryRow++;
        }

        $filename = 'deposit_portfolio_report_' . now()->format('Y-m-d') . '.xlsx';
        $filePath = storage_path('app/exports/' . $filename);
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Export interest report as Excel
     */
    public function exportInterestExcel(Request $request)
    {
        $query = LedgerEntry::where('entity_type', 'deposit')
            ->whereIn('type', ['accrual', 'interest'])
            ->with(['entity.member', 'createdBy']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }

        if ($request->filled('member_id')) {
            $query->whereHas('entity', function($q) use ($request) {
                $q->where('member_id', $request->member_id);
            });
        }

        $interestEntries = $query->orderBy('entry_date', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'A1' => 'Entry ID',
            'B1' => 'Deposit ID',
            'C1' => 'Member Name',
            'D1' => 'Entry Date',
            'E1' => 'Type',
            'F1' => 'Amount',
            'F1' => 'Balance After',
            'G1' => 'Description',
            'H1' => 'Created By',
            'I1' => 'Created At'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E8']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        foreach ($interestEntries as $entry) {
            $sheet->setCellValue('A' . $row, $entry->id);
            $sheet->setCellValue('B' . $row, $entry->entity_id);
            $sheet->setCellValue('C' . $row, $entry->entity->member->name);
            $sheet->setCellValue('D' . $row, $entry->entry_date->format('Y-m-d'));
            $sheet->setCellValue('E' . $row, ucfirst($entry->type));
            $sheet->setCellValue('F' . $row, $entry->amount);
            $sheet->setCellValue('G' . $row, $entry->balance_after);
            $sheet->setCellValue('H' . $row, $entry->description);
            $sheet->setCellValue('I' . $row, $entry->createdBy->name ?? 'System');
            $sheet->setCellValue('J' . $row, $entry->created_at->format('Y-m-d H:i:s'));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to data
        $dataRange = 'A1:J' . ($row - 1);
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        $filename = 'deposit_interest_report_' . now()->format('Y-m-d') . '.xlsx';
        $filePath = storage_path('app/exports/' . $filename);
        
        // Create directory if it doesn't exist
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }
}
