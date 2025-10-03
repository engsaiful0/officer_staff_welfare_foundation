<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\LedgerEntry;
use App\Models\Member;
use App\Models\Import;
use App\Services\InvestmentImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class InvestmentImportController extends Controller
{
    /**
     * Display the import form
     */
    public function index()
    {
        $recentImports = Import::with('importedBy')
            ->orderBy('imported_at', 'desc')
            ->limit(10)
            ->get();

        return view('content.content.investments.import', compact('recentImports'));
    }

    /**
     * Handle the Excel import
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'import_type' => 'required|in:investments,ledger_entries'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('file');
            $importType = $request->import_type;
            
            // Store the file
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('imports', $filename, 'local');

            DB::beginTransaction();

            $importRecord = Import::create([
                'filename' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'imported_by' => auth()->id(),
                'imported_at' => now(),
                'rows_imported' => 0,
                'errors' => []
            ]);

            $errors = [];
            $rowsImported = 0;

            $importService = new InvestmentImportService();

            if ($importType === 'investments') {
                $result = $importService->importInvestments($filePath, $importRecord->id);
                $rowsImported = $result['imported'];
                $errors = $result['errors'];
            } elseif ($importType === 'ledger_entries') {
                $result = $importService->importLedgerEntries($filePath, $importRecord->id);
                $rowsImported = $result['imported'];
                $errors = $result['errors'];
            }

            $importRecord->update([
                'rows_imported' => $rowsImported,
                'errors' => $errors
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Import completed. {$rowsImported} rows imported successfully.",
                    'data' => [
                        'rows_imported' => $rowsImported,
                        'errors' => $errors
                    ]
                ]);
            }

            return redirect()->back()->with([
                'success' => "Import completed. {$rowsImported} rows imported successfully.",
                'import_errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import failed: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Show import history
     */
    public function history()
    {
        $imports = Import::with('importedBy')
            ->orderBy('imported_at', 'desc')
            ->paginate(20);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $imports
            ]);
        }

        return view('investments.import-history', compact('imports'));
    }

    /**
     * Download import template
     */
    public function downloadTemplate($type)
    {
        if (!in_array($type, ['investments', 'ledger_entries'])) {
            return response()->json(['error' => 'Invalid template type'], 400);
        }

        $templatePath = $type === 'investments' 
            ? 'templates/investment_import_template.xlsx'
            : 'templates/ledger_entry_import_template.xlsx';

        if (Storage::exists($templatePath)) {
            return Storage::download($templatePath);
        }

        // Create template if it doesn't exist
        $this->createTemplate($type);
        
        return Storage::download($templatePath);
    }

    /**
     * Create import template
     */
    private function createTemplate($type)
    {
        if ($type === 'investments') {
            $headers = [
                'Member ID',
                'Principal Amount',
                'Product Name',
                'Start Date (YYYY-MM-DD)',
                'Term Months',
                'Rate (as decimal, e.g., 0.15 for 15%)',
                'Rate Period (annual/monthly)',
                'Frequency (monthly/quarterly/daily)',
                'Notes'
            ];
        } else {
            $headers = [
                'Investment ID',
                'Entry Date (YYYY-MM-DD)',
                'Type (accrual/payment/credit/adjustment)',
                'Amount',
                'Interest Amount',
                'Principal Amount',
                'Description'
            ];
        }

        $templateData = [$headers];
        
        Excel::store(
            new \Maatwebsite\Excel\Concerns\FromArray($templateData),
            "templates/{$type}_import_template.xlsx",
            'local'
        );
    }
}
