<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DepositLedgerController extends Controller
{
    use DepositLedgerControllerMethods;
    /**
     * Display ledger entries for a deposit
     */
    public function index(Deposit $deposit)
    {
        $ledgerEntries = $deposit->ledgerEntries()
            ->with('createdBy')
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $ledgerEntries
            ]);
        }

        return view('deposits.ledger.index', compact('deposit', 'ledgerEntries'));
    }

    /**
     * Record a deposit transaction
     */
    public function deposit(Request $request, Deposit $deposit)
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
            $newBalance = $currentBalance + $request->amount;

            $ledgerEntry = LedgerEntry::create([
                'entity_type' => 'deposit',
                'entity_id' => $deposit->id,
                'entry_date' => $request->entry_date,
                'type' => 'deposit',
                'amount' => $request->amount,
                'balance_after' => $newBalance,
                'description' => $request->description ?: 'Additional deposit',
                'created_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deposit recorded successfully',
                    'data' => $ledgerEntry
                ], 201);
            }

            return redirect()->back()->with('success', 'Deposit recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to record deposit: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to record deposit: ' . $e->getMessage());
        }
    }
}
