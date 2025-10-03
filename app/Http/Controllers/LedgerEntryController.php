<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LedgerEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LedgerEntry::with(['investment.member', 'createdBy']);

        // Apply filters
        if ($request->filled('investment_id')) {
            $query->where('investment_id', $request->investment_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }

        $ledgerEntries = $query->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $ledgerEntries
            ]);
        }

        $investments = Investment::with('member')->get();
        return view('ledger-entries.index', compact('ledgerEntries', 'investments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $investmentId = $request->get('investment_id');
        $investment = $investmentId ? Investment::with('member')->find($investmentId) : null;
        $investments = Investment::with('member')->get();

        return view('ledger-entries.create', compact('investment', 'investments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'investment_id' => 'required|exists:investments,id',
            'entry_date' => 'required|date',
            'type' => 'required|in:accrual,payment,credit,adjustment',
            'amount' => 'required|numeric|min:0',
            'interest_amount' => 'nullable|numeric|min:0',
            'principal_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string'
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
            DB::beginTransaction();

            $investment = Investment::findOrFail($request->investment_id);
            
            // Get current balance
            $currentBalance = $investment->current_balance;
            
            // Calculate new balance based on entry type
            $newBalance = $currentBalance;
            switch ($request->type) {
                case 'payment':
                    $newBalance -= $request->amount;
                    break;
                case 'credit':
                case 'accrual':
                    $newBalance += $request->amount;
                    break;
                case 'adjustment':
                    $newBalance += $request->amount; // Can be positive or negative
                    break;
            }

            $ledgerEntry = LedgerEntry::create([
                'investment_id' => $request->investment_id,
                'entry_date' => $request->entry_date,
                'type' => $request->type,
                'amount' => $request->amount,
                'interest_amount' => $request->interest_amount,
                'principal_amount' => $request->principal_amount,
                'balance_after' => $newBalance,
                'description' => $request->description,
                'created_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ledger entry created successfully',
                    'data' => $ledgerEntry->load(['investment.member', 'createdBy'])
                ], 201);
            }

            return redirect()->route('content.investments.show', $investment)
                ->with('success', 'Ledger entry created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create ledger entry: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create ledger entry: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LedgerEntry $ledgerEntry)
    {
        $ledgerEntry->load(['investment.member', 'createdBy']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $ledgerEntry
            ]);
        }

        return view('ledger-entries.show', compact('ledgerEntry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LedgerEntry $ledgerEntry)
    {
        $ledgerEntry->load('investment.member');
        $investments = Investment::with('member')->get();

        return view('ledger-entries.edit', compact('ledgerEntry', 'investments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LedgerEntry $ledgerEntry)
    {
        $validator = Validator::make($request->all(), [
            'entry_date' => 'required|date',
            'type' => 'required|in:accrual,payment,credit,adjustment',
            'amount' => 'required|numeric|min:0',
            'interest_amount' => 'nullable|numeric|min:0',
            'principal_amount' => 'nullable|numeric|min:0',
            'description' => 'nullable|string'
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
            DB::beginTransaction();

            // Recalculate balance if amount or type changed
            if ($ledgerEntry->amount != $request->amount || $ledgerEntry->type != $request->type) {
                // Get balance before this entry
                $previousEntry = LedgerEntry::where('investment_id', $ledgerEntry->investment_id)
                    ->where('entry_date', '<', $ledgerEntry->entry_date)
                    ->orderBy('entry_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();

                $balanceBefore = $previousEntry ? $previousEntry->balance_after : $ledgerEntry->investment->principal_amount;

                // Calculate new balance
                $newBalance = $balanceBefore;
                switch ($request->type) {
                    case 'payment':
                        $newBalance -= $request->amount;
                        break;
                    case 'credit':
                    case 'accrual':
                        $newBalance += $request->amount;
                        break;
                    case 'adjustment':
                        $newBalance += $request->amount;
                        break;
                }

                $ledgerEntry->update([
                    'entry_date' => $request->entry_date,
                    'type' => $request->type,
                    'amount' => $request->amount,
                    'interest_amount' => $request->interest_amount,
                    'principal_amount' => $request->principal_amount,
                    'balance_after' => $newBalance,
                    'description' => $request->description
                ]);

                // Update subsequent entries' balances
                $this->updateSubsequentBalances($ledgerEntry);
            } else {
                $ledgerEntry->update([
                    'entry_date' => $request->entry_date,
                    'interest_amount' => $request->interest_amount,
                    'principal_amount' => $request->principal_amount,
                    'description' => $request->description
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ledger entry updated successfully',
                    'data' => $ledgerEntry->load(['investment.member', 'createdBy'])
                ]);
            }

            return redirect()->route('content.investments.show', $ledgerEntry->investment)
                ->with('success', 'Ledger entry updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update ledger entry: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update ledger entry: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LedgerEntry $ledgerEntry)
    {
        try {
            // Don't allow deletion of principal entry
            if ($ledgerEntry->type === 'principal') {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete principal entry'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Cannot delete principal entry.');
            }

            DB::beginTransaction();

            $investment = $ledgerEntry->investment;
            $ledgerEntry->delete();

            // Recalculate subsequent balances
            $this->recalculateBalances($investment);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ledger entry deleted successfully'
                ]);
            }

            return redirect()->route('content.investments.show', $investment)
                ->with('success', 'Ledger entry deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete ledger entry: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete ledger entry: ' . $e->getMessage());
        }
    }

    /**
     * Update subsequent ledger entry balances
     */
    private function updateSubsequentBalances(LedgerEntry $ledgerEntry)
    {
        $subsequentEntries = LedgerEntry::where('investment_id', $ledgerEntry->investment_id)
            ->where(function($query) use ($ledgerEntry) {
                $query->where('entry_date', '>', $ledgerEntry->entry_date)
                      ->orWhere(function($q) use ($ledgerEntry) {
                          $q->where('entry_date', $ledgerEntry->entry_date)
                            ->where('created_at', '>', $ledgerEntry->created_at);
                      });
            })
            ->orderBy('entry_date')
            ->orderBy('created_at')
            ->get();

        $currentBalance = $ledgerEntry->balance_after;

        foreach ($subsequentEntries as $entry) {
            switch ($entry->type) {
                case 'payment':
                    $currentBalance -= $entry->amount;
                    break;
                case 'credit':
                case 'accrual':
                    $currentBalance += $entry->amount;
                    break;
                case 'adjustment':
                    $currentBalance += $entry->amount;
                    break;
            }

            $entry->update(['balance_after' => $currentBalance]);
        }
    }

    /**
     * Recalculate all balances for an investment
     */
    private function recalculateBalances(Investment $investment)
    {
        $entries = LedgerEntry::where('investment_id', $investment->id)
            ->orderBy('entry_date')
            ->orderBy('created_at')
            ->get();

        $currentBalance = $investment->principal_amount;

        foreach ($entries as $entry) {
            if ($entry->type === 'principal') {
                $currentBalance = $entry->amount;
            } else {
                switch ($entry->type) {
                    case 'payment':
                        $currentBalance -= $entry->amount;
                        break;
                    case 'credit':
                    case 'accrual':
                        $currentBalance += $entry->amount;
                        break;
                    case 'adjustment':
                        $currentBalance += $entry->amount;
                        break;
                }
            }

            $entry->update(['balance_after' => $currentBalance]);
        }
    }

    /**
     * Create interest accrual entry
     */
    public function createAccrual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'investment_id' => 'required|exists:investments,id',
            'entry_date' => 'required|date',
            'description' => 'nullable|string'
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
            DB::beginTransaction();

            $investment = Investment::findOrFail($request->investment_id);
            
            // Calculate interest based on rate and frequency
            $interestAmount = $this->calculateInterest($investment, $request->entry_date);
            
            if ($interestAmount <= 0) {
                throw new \Exception('No interest to accrue for this period');
            }

            $currentBalance = $investment->current_balance;
            $newBalance = $currentBalance + $interestAmount;

            $ledgerEntry = LedgerEntry::create([
                'investment_id' => $investment->id,
                'entry_date' => $request->entry_date,
                'type' => 'accrual',
                'amount' => $interestAmount,
                'interest_amount' => $interestAmount,
                'balance_after' => $newBalance,
                'description' => $request->description ?: 'Interest accrual',
                'created_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Interest accrual created successfully',
                    'data' => $ledgerEntry->load(['investment.member', 'createdBy'])
                ], 201);
            }

            return redirect()->route('content.investments.show', $investment)
                ->with('success', 'Interest accrual created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create interest accrual: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create interest accrual: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Calculate interest amount for an investment
     */
    private function calculateInterest(Investment $investment, $entryDate)
    {
        $currentBalance = $investment->current_balance;
        $rate = $investment->rate;
        
        // Get the last accrual date
        $lastAccrual = LedgerEntry::where('investment_id', $investment->id)
            ->where('type', 'accrual')
            ->orderBy('entry_date', 'desc')
            ->first();

        $lastAccrualDate = $lastAccrual ? $lastAccrual->entry_date : $investment->start_date;
        $daysSinceLastAccrual = Carbon::parse($lastAccrualDate)->diffInDays(Carbon::parse($entryDate));

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
