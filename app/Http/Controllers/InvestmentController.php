<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\Member;
use App\Models\InvestmentType;
use App\Models\InvestmentInstallment;
use App\Models\InvestmentAccount;
use App\Models\InvestmentAccountNumber;
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

        $investments = $query->with(['account', 'installments'])->orderBy('created_at', 'desc')->paginate(15);

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
        $latest = InvestmentAccountNumber::latest('serial')->first();
        $nextAccountNumber = 'INV' . Carbon::now()->year . '-' . str_pad($latest ? $latest->serial + 1 : 1, 6, '0', STR_PAD_LEFT);
        $investmentTypes=InvestmentType::all();
        return view('content.investments.create', compact('members', 'investmentTypes', 'nextAccountNumber'));
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
            'interest_rate' => 'required|numeric',
            'investment_years' => 'required|integer|min:1',
            'payment_type' => 'required|in:monthly,quarterly,yearly,daily',
            'no_of_installments' => 'required|integer|min:1',
            'principal_amount_per_installment' => 'required|numeric|min:0',
            'rent' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'total_rent' => 'required|numeric|min:0',
            'gestation_maturity_date' => 'nullable|date',
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

            // Calculate expiry date based on investment years
            $startDate = Carbon::parse($request->start_date);
            $expiryDate = $startDate->copy()->addYears((int) $request->investment_years);
            $termMonths = $request->investment_years * 12;

            // Determine rate_period and frequency based on payment_type
            $ratePeriod = 'annual'; // Default to annual
            $frequency = $request->payment_type; // monthly, quarterly, yearly, daily

            $investment = Investment::create([
                'member_id' => $request->member_id,
                'principal_amount' => $request->principal_amount,
                'product_name' => $request->product_name ?? null,
                'start_date' => $request->start_date,
                'term_months' => $termMonths,
                'expiry_date' => $expiryDate,
                'rate' => $request->interest_rate,
                'rate_period' => $ratePeriod,
                'frequency' => $frequency,
                'status' => 'active',
                'notes' => $request->notes
            ]);

            // Create initial ledger entry for principal
            LedgerEntry::create([
                'entity_type' => 'investment',
                'entity_id' => $investment->id,
                'entry_date' => $request->start_date,
                'type' => 'principal',
                'amount' => $request->principal_amount,
                'principal_amount' => $request->principal_amount,
                'balance_after' => $request->principal_amount,
                'description' => 'Initial investment principal',
                'created_by' => auth()->id()
            ]);

            // Generate installment schedule
            $this->generateInstallmentSchedule($investment, $request);

            // Create investment account
            $this->createInvestmentAccount($investment, $request);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment created successfully',
                    'data' => $investment->load(['member', 'account', 'installments']),
                    'redirect' => route('investments.show', $investment)
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
        $investment->load([
            'member', 
            'account.accountNumberRecord.user',
            'installments',
            'ledgerEntries.createdBy'
        ]);
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
            $expiryDate = $startDate->copy()->addMonths((int) $request->term_months);

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

    /**
     * Generate installment schedule for an investment
     */
    private function generateInstallmentSchedule(Investment $investment, Request $request)
    {
        $noOfInstallments = (int) $request->no_of_installments;
        $principalPerInstallment = (float) $request->principal_amount_per_installment;
        $rentPerInstallment = (float) $request->rent;
        $totalAmountPerInstallment = (float) $request->total_amount;
        $principalAmount = (float) $request->principal_amount;
        
        $startDate = Carbon::parse($request->start_date);
        $paymentType = $request->payment_type;
        
        $beginningBalance = $principalAmount;
        $cumulativeRent = 0;
        $installments = [];

        for ($i = 1; $i <= $noOfInstallments; $i++) {
            // Calculate schedule date based on payment type
            $scheduleDate = $startDate->copy();
            
            switch ($paymentType) {
                case 'monthly':
                    $scheduleDate->addMonths((int)($i - 1));
                    break;
                case 'quarterly':
                    $scheduleDate->addMonths((int)(($i - 1) * 3));
                    break;
                case 'yearly':
                    $scheduleDate->addYears((int)($i - 1));
                    break;
                case 'daily':
                    $scheduleDate->addDays((int)($i - 1));
                    break;
                default:
                    $scheduleDate->addMonths((int)($i - 1));
            }

            // Calculate ending balance
            $endingBalance = $beginningBalance - $principalPerInstallment;
            
            // Update cumulative rent
            $cumulativeRent += $rentPerInstallment;

            $installments[] = [
                'investment_id' => $investment->id,
                'installment_number' => $i,
                'schedule_date' => $scheduleDate->toDateString(),
                'beginning_balance' => round($beginningBalance, 2),
                'principal_amount' => round($principalPerInstallment, 2),
                'rent' => round($rentPerInstallment, 2),
                'total_amount' => round($totalAmountPerInstallment, 2),
                'ending_balance' => round($endingBalance, 2),
                'cumulative_rent' => round($cumulativeRent, 2),
                'status' => 'pending',
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Update beginning balance for next installment
            $beginningBalance = $endingBalance;
        }

        // Insert all installments in batch
        InvestmentInstallment::insert($installments);
    }

    /**
     * Create investment account for an investment
     */
    private function createInvestmentAccount(Investment $investment, Request $request)
    {
        $noOfInstallments = (int) $request->no_of_installments;
        
        $account = InvestmentAccount::create([
            'investment_id' => $investment->id,
            'account_opening_date' => $request->start_date,
            'opening_balance' => $request->principal_amount,
            'current_balance' => $request->principal_amount,
            'total_principal_paid' => 0,
            'total_interest_received' => 0,
            'total_rent_received' => 0,
            'total_payments_made' => 0,
            'total_installments_paid' => 0,
            'installments_paid_count' => 0,
            'installments_pending_count' => $noOfInstallments,
            'installments_overdue_count' => 0,
            'account_status' => 'active',
            'account_notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        // Generate account number
        $account->generateAccountNumber();
    }
}
