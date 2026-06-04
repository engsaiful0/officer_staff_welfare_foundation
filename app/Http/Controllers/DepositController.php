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
        $query = Deposit::with(['member']);

        // Apply filters
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('date_from')) {
            $query->where('deposit_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('deposit_date', '<=', $request->date_to);
        }

        $deposits = $query->orderBy('created_at', 'desc')->paginate(50);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $deposits
            ]);
        }

        $members = Member::select('id', 'name', 'member_unique_id')->get();
      
        
        return view('content.deposits.index', compact('deposits', 'members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
       $memberId = $request->get('member_id');
        $member = $memberId ? Member::find($memberId) : null;
        $members = Member::select('id', 'name', 'member_unique_id')->get();
        
        
        return view('content.deposits.create', compact('member', 'members'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'deposit_amount' => 'required|numeric|min:0',
            'deposit_date' => 'required|date',
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
                'deposit_amount' => $request->deposit_amount,
                'deposit_date' => $request->deposit_date,
                'notes' => $request->notes
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Deposit created successfully',
                    'data' => $deposit->load('member')
                ], 201);
            }

            return redirect()->route('deposits.index')
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
        $deposit->load(['member', 'ledgerEntries.createdBy']);
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
        $members = Member::select('id', 'name', 'member_unique_id')->get();
        return view('content.deposits.edit', compact('deposit', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deposit $deposit)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'deposit_amount' => 'required|numeric|min:0',
            'deposit_date' => 'required|date',
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
                'deposit_amount' => $request->deposit_amount,
                'deposit_date' => $request->deposit_date,
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

            return redirect()->route('deposits.index')
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
        $deposits = Deposit::with(['member', 'ledgerEntries' => function($q) {
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
