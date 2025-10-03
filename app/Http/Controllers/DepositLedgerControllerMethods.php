<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait DepositLedgerControllerMethods
{
    /**
     * Record a withdrawal transaction
     */
    public function withdrawal(Request $request, Deposit $deposit)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'description' => 'nullable|string|max:500'
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

            $currentBalance = $deposit->current_balance;
            
            if ($request->amount > $currentBalance) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Withdrawal amount cannot exceed current balance'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Withdrawal amount cannot exceed current balance.');
            }

            $newBalance = $currentBalance - $request->amount;

            $ledgerEntry = LedgerEntry::create([
                'entity_type' => 'deposit',
                'entity_id' => $deposit->id,
                'entry_date' => $request->entry_date,
                'type' => 'withdrawal',
                'amount' => $request->amount,
                'balance_after' => $newBalance,
                'description' => $request->description ?: 'Withdrawal',
                'created_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Withdrawal recorded successfully',
                    'data' => $ledgerEntry
                ], 201);
            }

            return redirect()->back()->with('success', 'Withdrawal recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to record withdrawal: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to record withdrawal: ' . $e->getMessage());
        }
    }

    /**
     * Record interest accrual
     */
    public function accrue(Request $request, Deposit $deposit)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
            'description' => 'nullable|string|max:500'
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

            $currentBalance = $deposit->current_balance;
            $newBalance = $currentBalance + $request->amount;

            $ledgerEntry = LedgerEntry::create([
                'entity_type' => 'deposit',
                'entity_id' => $deposit->id,
                'entry_date' => $request->entry_date,
                'type' => 'accrual',
                'amount' => $request->amount,
                'balance_after' => $newBalance,
                'description' => $request->description ?: 'Interest accrual',
                'created_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Interest accrual recorded successfully',
                    'data' => $ledgerEntry
                ], 201);
            }

            return redirect()->back()->with('success', 'Interest accrual recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to record interest accrual: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to record interest accrual: ' . $e->getMessage());
        }
    }

    /**
     * Record an adjustment
     */
    public function adjustment(Request $request, Deposit $deposit)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'entry_date' => 'required|date',
            'description' => 'required|string|max:500'
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

            $currentBalance = $deposit->current_balance;
            $newBalance = $currentBalance + $request->amount;

            $ledgerEntry = LedgerEntry::create([
                'entity_type' => 'deposit',
                'entity_id' => $deposit->id,
                'entry_date' => $request->entry_date,
                'type' => 'adjustment',
                'amount' => $request->amount,
                'balance_after' => $newBalance,
                'description' => $request->description,
                'created_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Adjustment recorded successfully',
                    'data' => $ledgerEntry
                ], 201);
            }

            return redirect()->back()->with('success', 'Adjustment recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to record adjustment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to record adjustment: ' . $e->getMessage());
        }
    }

    /**
     * Update a ledger entry
     */
    public function update(Request $request, Deposit $deposit, LedgerEntry $ledgerEntry)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'entry_date' => 'required|date',
            'description' => 'nullable|string|max:500'
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

            $ledgerEntry->update([
                'amount' => $request->amount,
                'entry_date' => $request->entry_date,
                'description' => $request->description
            ]);

            // Recalculate balances for all subsequent entries
            $this->recalculateBalances($deposit);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ledger entry updated successfully',
                    'data' => $ledgerEntry->fresh()
                ]);
            }

            return redirect()->back()->with('success', 'Ledger entry updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update ledger entry: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update ledger entry: ' . $e->getMessage());
        }
    }

    /**
     * Delete a ledger entry
     */
    public function destroy(Deposit $deposit, LedgerEntry $ledgerEntry)
    {
        try {
            DB::beginTransaction();

            // Don't allow deletion of the initial deposit entry
            if ($ledgerEntry->type === 'deposit' && $ledgerEntry->entry_date->format('Y-m-d') === $deposit->start_date->format('Y-m-d')) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete initial deposit entry'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Cannot delete initial deposit entry.');
            }

            $ledgerEntry->delete();

            // Recalculate balances for all subsequent entries
            $this->recalculateBalances($deposit);

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ledger entry deleted successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Ledger entry deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete ledger entry: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete ledger entry: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate balances for all ledger entries
     */
    private function recalculateBalances(Deposit $deposit)
    {
        $entries = $deposit->ledgerEntries()
            ->orderBy('entry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        $runningBalance = 0;

        foreach ($entries as $entry) {
            if ($entry->type === 'deposit' || $entry->type === 'accrual' || $entry->type === 'interest') {
                $runningBalance += $entry->amount;
            } elseif ($entry->type === 'withdrawal') {
                $runningBalance -= $entry->amount;
            } elseif ($entry->type === 'adjustment') {
                $runningBalance += $entry->amount;
            }

            $entry->update(['balance_after' => $runningBalance]);
        }
    }
}
