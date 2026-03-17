<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\DepositType;
use App\Models\Member;
use App\Models\LedgerEntry;
use App\Models\DepositAccountNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Deposit::with(['member', 'depositType', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }]);

        // Apply filters
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('deposit_type_id')) {
            $query->where('deposit_type_id', $request->deposit_type_id);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $deposits = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $deposits
            ]);
        }

        $members = Member::select('id', 'name', 'unique_id')->get();
        $depositTypes = DepositType::all();
        
        return view('content.deposits.index', compact('deposits', 'members', 'depositTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
       $memberId = $request->get('member_id');
        $member = $memberId ? Member::find($memberId) : null;
        $members = Member::select('id', 'name', 'unique_id')->get();
        
        
        return view('content.deposits.create', compact('member', 'members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'monthly_deposit_amount' => 'nullable|numeric|min:0',
            'deposit_day_of_month' => 'nullable|integer|min:1|max:31',
            'deposit_account_number' => 'required|string|max:255|unique:deposits,deposit_account_number',
            'start_date' => 'required|date',
            'maturity_date' => 'nullable|date|after:start_date',
            'rate' => 'nullable|numeric|min:0|max:100',
            'deposit_type_id' => 'required|exists:deposit_types,id',
            'notes' => 'nullable|string'
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

            // Convert rate from percentage to decimal (e.g., 8% -> 0.08)
            $rate = $request->rate ? $request->rate / 100 : null;
            
            $deposit = Deposit::create([
                'member_id' => $request->member_id,
                'monthly_deposit_amount' => $request->monthly_deposit_amount ?? null,
                'deposit_day_of_month' => $request->deposit_day_of_month ?? 1,
                'deposit_account_number' => $request->deposit_account_number,
                'start_date' => $request->start_date,
                'maturity_date' => $request->maturity_date,
                'rate' => $rate,
                'deposit_type_id' => $request->deposit_type_id,
                'status' => 'active',
                'notes' => $request->notes
            ]);

            // Note: Account number is already provided from the form, so generateAccountNumber() is not needed
            // If you need to track it in deposit_account_numbers table, you can add that logic here

            // Create initial ledger entry for deposit
            // Use monthly_deposit_amount if provided, otherwise 0 for account opening
            $initialAmount = $request->monthly_deposit_amount ?? 0;
            
            LedgerEntry::create([
                'entity_type' => 'deposit',
                'entity_id' => $deposit->id,
                'entry_date' => $request->start_date,
                'type' => 'deposit',
                'amount' => $initialAmount,
                'balance_after' => $initialAmount,
                'description' => $initialAmount > 0 ? 'Initial deposit / Account opening' : 'Account opening',
                'created_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deposit created successfully',
                    'data' => $deposit->load('member')
                ], 201);
            }

            return redirect()->route('deposits.show', $deposit)
                ->with('success', 'Deposit created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create deposit: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create deposit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Deposit $deposit)
    {
        $deposit->load(['member', 'depositType', 'ledgerEntries.createdBy']);
        $ledgerEntries = $deposit->ledgerEntries()->orderBy('entry_date', 'desc')->paginate(10);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'deposit' => $deposit,
                    'ledger_entries' => $ledgerEntries
                ]
            ]);
        }

        return view('content.deposits.show', compact('deposit', 'ledgerEntries'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deposit $deposit)
    {
        $members = Member::select('id', 'name', 'unique_id')->get();
        $depositTypes = DepositType::all();
        return view('content.deposits.edit', compact('deposit', 'members', 'depositTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deposit $deposit)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'monthly_deposit_amount' => 'nullable|numeric|min:0',
            'deposit_day_of_month' => 'nullable|integer|min:1|max:31',
            'start_date' => 'required|date',
            'maturity_date' => 'nullable|date|after:start_date',
            'rate' => 'nullable|numeric|min:0|max:100',
            'deposit_type_id' => 'required|exists:deposit_types,id',
            'status' => 'required|in:active,matured,closed',
            'notes' => 'nullable|string'
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

            // Convert rate from percentage to decimal (e.g., 8% -> 0.08)
            $rate = $request->rate ? $request->rate / 100 : null;

            $deposit->update([
                'member_id' => $request->member_id,
                'monthly_deposit_amount' => $request->monthly_deposit_amount ?? null,
                'deposit_day_of_month' => $request->deposit_day_of_month ?? 1,
                'start_date' => $request->start_date,
                'maturity_date' => $request->maturity_date,
                'rate' => $rate,
                'deposit_type_id' => $request->deposit_type_id,
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deposit updated successfully',
                    'data' => $deposit->load('member')
                ]);
            }

            return redirect()->route('deposits.show', $deposit)
                ->with('success', 'Deposit updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update deposit: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update deposit: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deposit $deposit)
    {
        try {
            // Check if deposit has ledger entries
            if ($deposit->ledgerEntries()->count() > 1) { // More than just the initial deposit entry
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete deposit with existing transactions'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Cannot delete deposit with existing transactions.');
            }

            $deposit->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deposit deleted successfully'
                ]);
            }

            return redirect()->route('deposits.view-deposits')
                ->with('success', 'Deposit deleted successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete deposit: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete deposit: ' . $e->getMessage());
        }
    }

    /**
     * Close a deposit
     */
    public function close(Deposit $deposit)
    {
        try {
            $deposit->update(['status' => 'closed']);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deposit closed successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Deposit closed successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to close deposit: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to close deposit: ' . $e->getMessage());
        }
    }

    /**
     * Get deposits for a specific member
     */
    public function getByMember($memberId)
    {
        $deposits = Deposit::with(['member', 'depositType', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }])
        ->where('member_id', $memberId)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $deposits
        ]);
    }
}
