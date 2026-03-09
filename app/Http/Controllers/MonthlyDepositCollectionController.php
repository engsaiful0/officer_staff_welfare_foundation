<?php

namespace App\Http\Controllers;

use App\Models\MonthlyDepositCollection;
use App\Models\Deposit;
use App\Models\Member;
use App\Models\LedgerEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class MonthlyDepositCollectionController extends Controller
{
    /**
     * Display a listing of monthly deposit collections with filters
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getCollections($request);
        }

        $deposits = Deposit::with('member')
            ->where('status', 'active')
            ->whereNotNull('monthly_deposit_amount')
            ->where('monthly_deposit_amount', '>', 0)
            ->orderBy('deposit_account_number')
            ->get();
        
        $members = Member::select('id', 'name', 'unique_id')->get();

        return view('content.deposits.monthly-collections.index', compact('deposits', 'members'));
    }

    /**
     * Get collections with filters (AJAX)
     */
    private function getCollections(Request $request)
    {
        $query = MonthlyDepositCollection::with(['deposit', 'member', 'createdBy']);
        $this->applyFilters($query, $request);

        // Get total amount before pagination (without orderBy for better performance)
        $totalAmountQuery = MonthlyDepositCollection::query();
        $this->applyFilters($totalAmountQuery, $request);
        $totalAmount = $totalAmountQuery->sum('amount');

        $collections = $query->orderBy('collection_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Calculate current page total
        $currentPageTotal = collect($collections->items())->sum('amount');

        return response()->json([
            'success' => true,
            'data' => $collections->items(),
            'pagination' => (string) $collections->links(),
            'summary' => [
                'total_collections' => $collections->total(),
                'total_amount' => $totalAmount,
                'current_page_total' => $currentPageTotal
            ]
        ]);
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('deposit_id')) {
            $query->where('deposit_id', $request->deposit_id);
        }

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('collection_number')) {
            $query->where('collection_number', 'like', '%' . $request->collection_number . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('collection_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('collection_date', '<=', $request->date_to);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('collection_number', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('deposit', function($dq) use ($search) {
                      $dq->where('deposit_account_number', 'like', "%$search%");
                  })
                  ->orWhereHas('member', function($mq) use ($search) {
                      $mq->where('name', 'like', "%$search%")
                        ->orWhere('unique_id', 'like', "%$search%");
                  });
            });
        }
    }

    /**
     * Show the form for creating a new collection
     */
    public function create()
    {
        $deposits = Deposit::with('member')
            ->where('status', 'active')
            ->whereNotNull('monthly_deposit_amount')
            ->where('monthly_deposit_amount', '>', 0)
            ->orderBy('deposit_account_number')
            ->get();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'deposits' => $deposits->map(function($deposit) {
                        return [
                            'id' => $deposit->id,
                            'deposit_account_number' => $deposit->deposit_account_number,
                            'member_name' => $deposit->member->name,
                            'member_id' => $deposit->member->unique_id,
                            'monthly_deposit_amount' => (float)$deposit->monthly_deposit_amount,
                            'current_balance' => (float)$deposit->current_balance
                        ];
                    })
                ]
            ]);
        }

        return view('content.deposits.monthly-collections.create', compact('deposits'));
    }

    /**
     * Store a newly created collection
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deposit_id' => 'required|exists:deposits,id',
            'collection_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'nullable|string|max:20',
            'year' => 'nullable|integer|min:2000|max:2100',
            'month_year' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $deposit = Deposit::with('member')->findOrFail($request->deposit_id);

            // Generate collection number
            $collectionNumber = MonthlyDepositCollection::generateCollectionNumber();

            // Get month string from month_year or month+year or generate from date
            $month = $request->month_year;
            if (!$month) {
                if ($request->month && $request->year) {
                    $month = $request->month . ' ' . $request->year;
                } else {
                    $month = Carbon::parse($request->collection_date)->format('F Y');
                }
            }

            // Create collection
            $collection = MonthlyDepositCollection::create([
                'deposit_id' => $deposit->id,
                'member_id' => $deposit->member_id,
                'collection_number' => $collectionNumber,
                'collection_date' => $request->collection_date,
                'amount' => $request->amount,
                'month' => $month,
                'description' => $request->description,
                'created_by' => auth()->id()
            ]);

            // Create ledger entry
            $currentBalance = $deposit->current_balance;
            $newBalance = $currentBalance + $request->amount;

            LedgerEntry::create([
                'entity_type' => 'deposit',
                'entity_id' => $deposit->id,
                'entry_date' => $request->collection_date,
                'type' => 'deposit',
                'amount' => $request->amount,
                'balance_after' => $newBalance,
                'description' => $request->description ?? ('Monthly deposit collection - ' . $month),
                'created_by' => auth()->id()
            ]);

            // Update deposit's last_deposit_date
            $deposit->update([
                'last_deposit_date' => $request->collection_date
            ]);

            DB::commit();

            $collection->load(['deposit', 'member', 'createdBy']);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Monthly deposit collection recorded successfully! Collection Number: ' . $collectionNumber,
                    'data' => $collection,
                    'redirect' => route('deposits.monthly-collections.invoice', $collection->id)
                ], 201);
            }

            return redirect()->route('deposits.monthly-collections.invoice', $collection->id)
                ->with('success', 'Monthly deposit collection recorded successfully! Collection Number: ' . $collectionNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create collection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified collection
     */
    public function show(Request $request, $id)
    {
        $collection = MonthlyDepositCollection::with(['deposit', 'member', 'createdBy'])
            ->findOrFail($id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $collection
            ]);
        }

        return view('content.deposits.monthly-collections.invoice', compact('collection'));
    }

    /**
     * Display invoice for a collection
     */
    public function invoice($id)
    {
        $collection = MonthlyDepositCollection::with(['deposit', 'member', 'createdBy'])
            ->findOrFail($id);

        return view('content.deposits.monthly-collections.invoice', compact('collection'));
    }

    /**
     * Show the form for editing the specified collection
     */
    public function edit($id)
    {
        $collection = MonthlyDepositCollection::with(['deposit', 'member'])
            ->findOrFail($id);

        $deposits = Deposit::with('member')
            ->where('status', 'active')
            ->whereNotNull('monthly_deposit_amount')
            ->where('monthly_deposit_amount', '>', 0)
            ->orderBy('deposit_account_number')
            ->get();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'collection' => $collection,
                    'deposits' => $deposits->map(function($deposit) {
                        return [
                            'id' => $deposit->id,
                            'deposit_account_number' => $deposit->deposit_account_number,
                            'member_name' => $deposit->member->name,
                            'member_id' => $deposit->member->unique_id,
                            'monthly_deposit_amount' => (float)$deposit->monthly_deposit_amount
                        ];
                    })
                ]
            ]);
        }

        return view('content.deposits.monthly-collections.edit', compact('collection', 'deposits'));
    }

    /**
     * Update the specified collection
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'deposit_id' => 'required|exists:deposits,id',
            'collection_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'month' => 'nullable|string|max:20',
            'year' => 'nullable|integer|min:2000|max:2100',
            'month_year' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $collection = MonthlyDepositCollection::with('deposit')->findOrFail($id);
            $oldAmount = $collection->amount;
            $oldDate = $collection->collection_date;

            // Get month string from month_year or month+year or generate from date
            $month = $request->month_year;
            if (!$month) {
                if ($request->month && $request->year) {
                    $month = $request->month . ' ' . $request->year;
                } else {
                    $month = Carbon::parse($request->collection_date)->format('F Y');
                }
            }

            // Update collection
            $collection->update([
                'deposit_id' => $request->deposit_id,
                'member_id' => Deposit::find($request->deposit_id)->member_id,
                'collection_date' => $request->collection_date,
                'amount' => $request->amount,
                'month' => $month,
                'description' => $request->description
            ]);

            // Find and update corresponding ledger entry
            $ledgerEntry = LedgerEntry::where('entity_type', 'deposit')
                ->where('entity_id', $collection->deposit_id)
                ->where('entry_date', $oldDate)
                ->where('type', 'deposit')
                ->where('amount', $oldAmount)
                ->where('description', 'like', '%Monthly deposit collection%')
                ->first();

            if ($ledgerEntry) {
                // Recalculate balance after this entry
                $deposit = Deposit::find($collection->deposit_id);
                $entriesAfter = LedgerEntry::where('entity_type', 'deposit')
                    ->where('entity_id', $deposit->id)
                    ->where(function($q) use ($oldDate, $oldAmount) {
                        $q->where('entry_date', '>', $oldDate)
                          ->orWhere(function($q2) use ($oldDate, $oldAmount) {
                              $q2->where('entry_date', $oldDate)
                                 ->where('created_at', '>', $ledgerEntry->created_at);
                          });
                    })
                    ->orderBy('entry_date')
                    ->orderBy('created_at')
                    ->get();

                $balanceBefore = $deposit->current_balance - $oldAmount;
                $newBalance = $balanceBefore + $request->amount;

                $ledgerEntry->update([
                    'entry_date' => $request->collection_date,
                    'amount' => $request->amount,
                    'balance_after' => $newBalance,
                    'description' => $request->description ?? ('Monthly deposit collection - ' . $month)
                ]);

                // Recalculate balances for subsequent entries
                $runningBalance = $newBalance;
                foreach ($entriesAfter as $entry) {
                    if ($entry->type === 'deposit' || $entry->type === 'accrual' || $entry->type === 'interest') {
                        $runningBalance += $entry->amount;
                    } elseif ($entry->type === 'withdrawal') {
                        $runningBalance -= $entry->amount;
                    }
                    $entry->update(['balance_after' => $runningBalance]);
                }
            }

            DB::commit();

            $collection->load(['deposit', 'member', 'createdBy']);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Monthly deposit collection updated successfully!',
                    'data' => $collection,
                    'redirect' => route('deposits.monthly-collections.invoice', $collection->id)
                ]);
            }

            return redirect()->route('deposits.monthly-collections.invoice', $collection->id)
                ->with('success', 'Monthly deposit collection updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update collection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified collection
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $collection = MonthlyDepositCollection::with('deposit')->findOrFail($id);
            $deposit = $collection->deposit;

            // Find and delete corresponding ledger entry
            $ledgerEntry = LedgerEntry::where('entity_type', 'deposit')
                ->where('entity_id', $deposit->id)
                ->where('entry_date', $collection->collection_date)
                ->where('type', 'deposit')
                ->where('amount', $collection->amount)
                ->where('description', 'like', '%Monthly deposit collection%')
                ->first();

            if ($ledgerEntry) {
                // Recalculate balances for subsequent entries
                $entriesAfter = LedgerEntry::where('entity_type', 'deposit')
                    ->where('entity_id', $deposit->id)
                    ->where(function($q) use ($collection) {
                        $q->where('entry_date', '>', $collection->collection_date)
                          ->orWhere(function($q2) use ($collection, $ledgerEntry) {
                              $q2->where('entry_date', $collection->collection_date)
                                 ->where('created_at', '>', $ledgerEntry->created_at);
                          });
                    })
                    ->orderBy('entry_date')
                    ->orderBy('created_at')
                    ->get();

                $balanceBefore = $ledgerEntry->balance_after - $ledgerEntry->amount;
                $runningBalance = $balanceBefore;

                foreach ($entriesAfter as $entry) {
                    if ($entry->type === 'deposit' || $entry->type === 'accrual' || $entry->type === 'interest') {
                        $runningBalance += $entry->amount;
                    } elseif ($entry->type === 'withdrawal') {
                        $runningBalance -= $entry->amount;
                    }
                    $entry->update(['balance_after' => $runningBalance]);
                }

                $ledgerEntry->delete();
            }

            $collection->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Monthly deposit collection deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete collection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export collections to PDF, Excel or Print
     */
    public function export(Request $request)
    {
        $type = $request->type; // pdf, excel, print
        
        // If single collection ID is provided, export single receipt
        if ($request->filled('collection_id')) {
            $collection = MonthlyDepositCollection::with(['deposit', 'member', 'createdBy'])
                ->findOrFail($request->collection_id);
            
            if ($type === 'pdf') {
                $pdf = Pdf::loadView('content.deposits.monthly-collections.receipt-pdf', compact('collection'));
                return $pdf->download('receipt-' . $collection->collection_number . '.pdf');
            }
            
            if ($type === 'print') {
                return view('content.deposits.monthly-collections.receipt-print', compact('collection'));
            }
        }
        
        // Get collections with same filters as index
        $query = MonthlyDepositCollection::with(['deposit', 'member', 'createdBy']);
        $this->applyFilters($query, $request);

        $collections = $query->orderBy('collection_date', 'desc')->get();
        $summary = [
            'total_collections' => $collections->count(),
            'total_amount' => $collections->sum('amount')
        ];

        if ($type === 'pdf') {
            $pdf = Pdf::loadView('content.deposits.monthly-collections.export-pdf', compact('collections', 'summary'));
            return $pdf->download('monthly-deposit-collections-' . date('Y-m-d') . '.pdf');
        }

        if ($type === 'excel') {
            return Excel::download(new class($collections) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                private $collections;
                public function __construct($collections) { $this->collections = $collections; }
                public function collection() {
                    return $this->collections->map(function($item) {
                        return [
                            $item->collection_date->format('Y-m-d'),
                            $item->collection_number,
                            $item->deposit->deposit_account_number ?? 'N/A',
                            $item->member->name ?? 'N/A',
                            $item->member->unique_id ?? 'N/A',
                            $item->amount,
                            $item->month ?? 'N/A',
                            $item->description ?? 'N/A',
                            $item->createdBy->name ?? 'N/A'
                        ];
                    });
                }
                public function headings(): array {
                    return ['Date', 'Collection #', 'Account #', 'Member Name', 'Member ID', 'Amount', 'Month', 'Description', 'Collected By'];
                }
            }, 'monthly-deposit-collections-' . date('Y-m-d') . '.xlsx');
        }

        if ($type === 'print') {
            return view('content.deposits.monthly-collections.export-print', compact('collections', 'summary'));
        }

        return redirect()->back();
    }
}
