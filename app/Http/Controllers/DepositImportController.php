<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Services\DepositImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DepositImportController extends Controller
{
    protected $importService;

    public function __construct(DepositImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Show the import form
     */
    public function index()
    {
        $imports = Import::where('type', 'deposit')
            ->orderBy('imported_at', 'desc')
            ->paginate(10);

        return view('deposits.import', compact('imports'));
    }

    /**
     * Handle Excel file upload and import
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240' // 10MB max
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator);
        }

        try {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('imports/deposits', $filename, 'local');

            // Create import record
            $import = Import::create([
                'filename' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'type' => 'deposit',
                'imported_by' => auth()->id(),
                'imported_at' => now(),
                'rows_imported' => 0,
                'errors' => []
            ]);

            // Process the import
            $result = $this->importService->importFromFile($filePath, $import);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Import completed',
                    'data' => $result
                ]);
            }

            return redirect()->back()->with('success', 'Import completed successfully.');

        } catch (\Exception $e) {
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
     * Download import template
     */
    public function downloadTemplate()
    {
        $templatePath = $this->importService->generateTemplate();
        
        return response()->download($templatePath, 'deposit_import_template.xlsx')
            ->deleteFileAfterSend(true);
    }

    /**
     * View import history
     */
    public function history()
    {
        $imports = Import::where('type', 'deposit')
            ->with('importedBy')
            ->orderBy('imported_at', 'desc')
            ->paginate(20);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $imports
            ]);
        }

        return view('deposits.import-history', compact('imports'));
    }

    /**
     * View import details
     */
    public function show(Import $import)
    {
        if ($import->type !== 'deposit') {
            abort(404);
        }

        $import->load('importedBy');

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $import
            ]);
        }

        return view('deposits.import-details', compact('import'));
    }
}
