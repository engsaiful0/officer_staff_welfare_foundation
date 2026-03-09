<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentAccount;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvestmentLedgerExport;

class InvestmentLedgerController extends Controller
{
    /**
     * Show investment account ledger selection page
     */
    public function accountLedger(Request $request)
    {
        $investments = Investment::with(['member', 'account'])
            ->where('status', 'active')
            ->whereHas('account', function($query) {
                $query->where('account_status', 'active');
            })
            ->get()
            ->map(function($investment) {
                $account = $investment->account;
                return [
                    'id' => $investment->id,
                    'account_number' => $account->account_number ?? 'N/A',
                    'member_name' => $investment->member->name ?? 'N/A',
                    'member_id' => $investment->member->unique_id ?? 'N/A',
                    'display' => ($account->account_number ?? 'N/A') . ' - ' . ($investment->member->name ?? 'N/A')
                ];
            })
            ->sortBy('account_number')
            ->values();

        return view('content.investments.account-ledger', compact('investments'));
    }

    /**
     * Get investment account ledger with days and product calculations
     */
    public function getAccountLedger(Request $request, $investmentId)
    {
        $validator = Validator::make(['investment_id' => $investmentId], [
            'investment_id' => 'required|exists:investments,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid investment account ID',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $investment = Investment::with(['member', 'account'])->findOrFail($investmentId);

            // Get all ledger entries for this investment, ordered by date
            $ledgerEntries = LedgerEntry::where('entity_type', 'investment')
                ->where('entity_id', $investment->id)
                ->orderBy('entry_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            // Build ledger data with days and product calculations
            $ledgerData = [];
            $investmentStartDate = Carbon::parse($investment->start_date);

            // Add initial balance row if first entry date is after investment start date
            if ($ledgerEntries->count() > 0) {
                $firstEntry = $ledgerEntries->first();
                $firstEntryDate = Carbon::parse($firstEntry->entry_date);

                if ($investmentStartDate->lt($firstEntryDate)) {
                    $endingDate = $firstEntryDate->copy()->subDay();
                    $days = $investmentStartDate->diffInDays($endingDate) + 1;

                    $ledgerData[] = [
                        'date' => $investmentStartDate->format('d/m/Y'),
                        'ending_date' => $endingDate->format('d/m/Y'),
                        'particulars' => 'To disburse',
                        'debit' => $investment->principal_amount,
                        'credit' => 0,
                        'balance' => $investment->principal_amount,
                        'days' => $days,
                        'product' => $investment->principal_amount * $days,
                        'entry_date' => $investmentStartDate->format('Y-m-d'),
                        'type' => 'balance'
                    ];
                }
            } elseif ($ledgerEntries->count() === 0) {
                // No entries yet, show initial balance row
                $endingDate = Carbon::today();
                $days = $investmentStartDate->diffInDays($endingDate) + 1;

                $ledgerData[] = [
                    'date' => $investmentStartDate->format('d/m/Y'),
                    'ending_date' => $endingDate->format('d/m/Y'),
                    'particulars' => 'To disburse',
                    'debit' => $investment->principal_amount,
                    'credit' => 0,
                    'balance' => $investment->principal_amount,
                    'days' => $days,
                    'product' => $investment->principal_amount * $days,
                    'entry_date' => $investmentStartDate->format('Y-m-d'),
                    'type' => 'balance'
                ];
            }

            foreach ($ledgerEntries as $index => $entry) {
                $currentDate = Carbon::parse($entry->entry_date);
                $endingDate = null;
                $days = 0;

                // Calculate ending date (next entry date or today if last entry)
                if ($index < $ledgerEntries->count() - 1) {
                    $nextEntry = $ledgerEntries[$index + 1];
                    $endingDate = Carbon::parse($nextEntry->entry_date)->subDay();
                } else {
                    $endingDate = Carbon::today();
                }

                // Calculate days in period
                $days = $currentDate->diffInDays($endingDate) + 1;

                // Determine debit and credit based on entry type
                $debit = 0;
                $credit = 0;
                
                // For investments:
                // - Principal disbursement: Debit
                // - Payments received (principal/rent): Credit
                // - Adjustments: Can be either
                if ($entry->type === 'principal' || $entry->type === 'disbursement') {
                    $debit = $entry->amount;
                } elseif ($entry->type === 'payment' || $entry->type === 'credit') {
                    $credit = $entry->amount;
                } elseif ($entry->type === 'adjustment') {
                    // Adjustments can be debit or credit based on amount sign
                    if ($entry->amount > 0) {
                        $credit = $entry->amount;
                    } else {
                        $debit = abs($entry->amount);
                    }
                } elseif ($entry->type === 'accrual') {
                    // Accruals are typically credits (interest earned)
                    $credit = $entry->interest_amount ?? $entry->amount;
                }

                // Get balance after this transaction
                $balance = $entry->balance_after ?? 0;

                // Calculate product (Balance * Days)
                $product = $balance * $days;

                // Determine particulars
                $particulars = $entry->description;
                if (empty($particulars)) {
                    $particulars = ucfirst($entry->type);
                    if ($entry->type === 'payment' && $entry->principal_amount) {
                        $particulars = 'Payment (Principal: ' . number_format($entry->principal_amount, 2) . ')';
                    }
                }

                $ledgerData[] = [
                    'date' => $currentDate->format('d/m/Y'),
                    'ending_date' => $endingDate->format('d/m/Y'),
                    'particulars' => $particulars,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'days' => $days,
                    'product' => $product,
                    'entry_date' => $currentDate->format('Y-m-d'),
                    'type' => $entry->type
                ];
            }

            $totalProduct = collect($ledgerData)->sum('product');
            $totalBalance = !empty($ledgerData) ? end($ledgerData)['balance'] : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'investment' => [
                        'id' => $investment->id,
                        'account_number' => $investment->account->account_number ?? 'N/A',
                        'member_name' => $investment->member->name ?? 'N/A',
                        'member_id' => $investment->member->unique_id ?? 'N/A',
                        'start_date' => $investment->start_date->format('d/m/Y'),
                        'principal_amount' => $investment->principal_amount,
                    ],
                    'ledger' => $ledgerData,
                    'total_product' => $totalProduct,
                    'total_balance' => $totalBalance
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load ledger: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export investment account ledger
     */
    public function exportAccountLedger(Request $request, $investmentId)
    {
        $type = $request->get('type', 'print'); // pdf, excel, print
        $investment = Investment::with(['member', 'account'])->findOrFail($investmentId);

        // Get ledger data (reuse the same logic)
        $ledgerEntries = LedgerEntry::where('entity_type', 'investment')
            ->where('entity_id', $investment->id)
            ->orderBy('entry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $ledgerData = [];
        $investmentStartDate = Carbon::parse($investment->start_date);

        // Add initial balance row if first entry date is after investment start date
        if ($ledgerEntries->count() > 0) {
            $firstEntry = $ledgerEntries->first();
            $firstEntryDate = Carbon::parse($firstEntry->entry_date);

            if ($investmentStartDate->lt($firstEntryDate)) {
                $endingDate = $firstEntryDate->copy()->subDay();
                $days = $investmentStartDate->diffInDays($endingDate) + 1;

                $ledgerData[] = [
                    'date' => $investmentStartDate->format('d/m/Y'),
                    'ending_date' => $endingDate->format('d/m/Y'),
                    'particulars' => 'To disburse',
                    'debit' => $investment->principal_amount,
                    'credit' => 0,
                    'balance' => $investment->principal_amount,
                    'days' => $days,
                    'product' => $investment->principal_amount * $days,
                ];
            }
        } elseif ($ledgerEntries->count() === 0) {
            $endingDate = Carbon::today();
            $days = $investmentStartDate->diffInDays($endingDate) + 1;

            $ledgerData[] = [
                'date' => $investmentStartDate->format('d/m/Y'),
                'ending_date' => $endingDate->format('d/m/Y'),
                'particulars' => 'To disburse',
                'debit' => $investment->principal_amount,
                'credit' => 0,
                'balance' => $investment->principal_amount,
                'days' => $days,
                'product' => $investment->principal_amount * $days,
            ];
        }

        foreach ($ledgerEntries as $index => $entry) {
            $currentDate = Carbon::parse($entry->entry_date);
            $endingDate = null;
            $days = 0;

            if ($index < $ledgerEntries->count() - 1) {
                $nextEntry = $ledgerEntries[$index + 1];
                $endingDate = Carbon::parse($nextEntry->entry_date)->subDay();
            } else {
                $endingDate = Carbon::today();
            }

            $days = $currentDate->diffInDays($endingDate) + 1;

            $debit = 0;
            $credit = 0;
            
            if ($entry->type === 'principal' || $entry->type === 'disbursement') {
                $debit = $entry->amount;
            } elseif ($entry->type === 'payment' || $entry->type === 'credit') {
                $credit = $entry->amount;
            } elseif ($entry->type === 'adjustment') {
                if ($entry->amount > 0) {
                    $credit = $entry->amount;
                } else {
                    $debit = abs($entry->amount);
                }
            } elseif ($entry->type === 'accrual') {
                $credit = $entry->interest_amount ?? $entry->amount;
            }

            $balance = $entry->balance_after ?? 0;
            $product = $balance * $days;

            $particulars = $entry->description;
            if (empty($particulars)) {
                $particulars = ucfirst($entry->type);
                if ($entry->type === 'payment' && $entry->principal_amount) {
                    $particulars = 'Payment (Principal: ' . number_format($entry->principal_amount, 2) . ')';
                }
            }

            $ledgerData[] = [
                'date' => $currentDate->format('d/m/Y'),
                'ending_date' => $endingDate->format('d/m/Y'),
                'particulars' => $particulars,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'days' => $days,
                'product' => $product,
            ];
        }

        $totalProduct = collect($ledgerData)->sum('product');
        $totalBalance = !empty($ledgerData) ? end($ledgerData)['balance'] : 0;

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('content.investments.account-ledger-export-pdf', [
                'investment' => $investment,
                'ledger' => $ledgerData,
                'total_product' => $totalProduct,
                'total_balance' => $totalBalance
            ]);
            return $pdf->download('investment-ledger-' . ($investment->account->account_number ?? $investment->id) . '.pdf');
        } elseif ($type === 'excel') {
            return Excel::download(new InvestmentLedgerExport($ledgerData, $investment, $totalProduct, $totalBalance), 
                'investment-ledger-' . ($investment->account->account_number ?? $investment->id) . '.xlsx');
        } else {
            // Print view
            return view('content.investments.account-ledger-export-print', [
                'investment' => $investment,
                'ledger' => $ledgerData,
                'total_product' => $totalProduct,
                'total_balance' => $totalBalance
            ]);
        }
    }
}





