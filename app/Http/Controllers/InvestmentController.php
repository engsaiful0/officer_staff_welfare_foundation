<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Member;
use App\Models\InvestmentType;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InvestmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Investment::with(['member', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }]);

        // Apply filters
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $investments = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $investments
            ]);
        }

        $members = Member::select('id', 'name', 'unique_id')->get();
        
        return view('content.investments.index', compact('investments', 'members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::select('id', 'name', 'unique_id')->get();
        $investmentTypes=InvestmentType::all();
        return view('content.investments.create', compact('members', 'investmentTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'principal_amount' => 'required|numeric|min:0',
            'investment_type_id' => 'required|exists:investment_types,id',
            'start_date' => 'required|date',
            'term_months' => 'required|integer|min:1',
            'rate' => 'required|numeric|min:0|max:1',
            'rate_period' => 'required|in:annual,monthly',
            'frequency' => 'required|in:monthly,quarterly,daily',
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

            // Calculate expiry date
            $startDate = Carbon::parse($request->start_date);
            $expiryDate = $startDate->copy()->addMonths($request->term_months);

            $investment = Investment::create([
                'member_id' => $request->member_id,
                'principal_amount' => $request->principal_amount,
                'product_name' => $request->product_name,
                'start_date' => $request->start_date,
                'term_months' => $request->term_months,
                'expiry_date' => $expiryDate,
                'rate' => $request->rate,
                'rate_period' => $request->rate_period,
                'frequency' => $request->frequency,
                'status' => 'active',
                'notes' => $request->notes
            ]);

            // Create initial ledger entry for principal
            LedgerEntry::create([
                'investment_id' => $investment->id,
                'entry_date' => $request->start_date,
                'type' => 'principal',
                'amount' => $request->principal_amount,
                'principal_amount' => $request->principal_amount,
                'balance_after' => $request->principal_amount,
                'description' => 'Initial investment principal',
                'created_by' => auth()->id()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment created successfully',
                    'data' => $investment->load('member')
                ], 201);
            }

            return redirect()->route('investments.show', $investment)
                ->with('success', 'Investment created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create investment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create investment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Investment $investment)
    {
        $investment->load(['member', 'ledgerEntries.createdBy']);
        $ledgerEntries = $investment->ledgerEntries()->orderBy('entry_date', 'desc')->paginate(10);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'investment' => $investment,
                    'ledger_entries' => $ledgerEntries
                ]
            ]);
        }

        return view('content.investments.show', compact('investment', 'ledgerEntries'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Investment $investment)
    {
        $members = Member::select('id', 'name', 'unique_id')->get();
        return view('content.investments.edit', compact('investment', 'members'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Investment $investment)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'principal_amount' => 'required|numeric|min:0',
            'product_name' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'term_months' => 'required|integer|min:1',
            'rate' => 'required|numeric|min:0|max:1',
            'rate_period' => 'required|in:annual,monthly',
            'frequency' => 'required|in:monthly,quarterly,daily',
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

            // Check if rate has changed
            $rateChanged = $investment->rate != $request->rate;
            $oldRate = $investment->rate;

            // Calculate expiry date
            $startDate = Carbon::parse($request->start_date);
            $expiryDate = $startDate->copy()->addMonths($request->term_months);

            $investment->update([
                'member_id' => $request->member_id,
                'principal_amount' => $request->principal_amount,
                'product_name' => $request->product_name,
                'start_date' => $request->start_date,
                'term_months' => $request->term_months,
                'expiry_date' => $expiryDate,
                'rate' => $request->rate,
                'rate_period' => $request->rate_period,
                'frequency' => $request->frequency,
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            // Create rate history entry if rate changed
            if ($rateChanged) {
                \App\Models\RateHistory::create([
                    'investment_id' => $investment->id,
                    'old_rate' => $oldRate,
                    'new_rate' => $request->rate,
                    'effective_date' => now()->toDateString(),
                    'reason' => 'Rate updated via investment edit',
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment updated successfully',
                    'data' => $investment->load('member')
                ]);
            }

            return redirect()->route('investments.show', $investment)
                ->with('success', 'Investment updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update investment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update investment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Investment $investment)
    {
        try {
            // Check if investment has ledger entries
            if ($investment->ledgerEntries()->count() > 1) { // More than just the initial principal entry
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete investment with existing transactions'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Cannot delete investment with existing transactions.');
            }

            $investment->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment deleted successfully'
                ]);
            }

            return redirect()->route('investments.index')
                ->with('success', 'Investment deleted successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete investment: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete investment: ' . $e->getMessage());
        }
    }

    /**
     * Get investments for a specific member
     */
    public function getByMember($memberId)
    {
        $investments = Investment::with(['member', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }])
        ->where('member_id', $memberId)
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $investments
        ]);
    }
}
