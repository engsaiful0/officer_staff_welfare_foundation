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

        $members = Member::select('id', 'name', 'member_unique_id')->get();
        
        return view('content.investments.index', compact('investments', 'members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = Member::select('id', 'name', 'member_unique_id')->get();
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
        $investment->load(['account', 'installments']);
        $members = Member::select('id', 'name', 'member_unique_id')->get();
        $investmentTypes = InvestmentType::all();
        
        // Calculate values for form
        $noOfInstallments = $investment->installments->count();
        $principalPerInstallment = $noOfInstallments > 0 ? $investment->installments->first()->principal_amount : 0;
        $rentPerInstallment = $noOfInstallments > 0 ? $investment->installments->first()->rent : 0;
        $totalAmountPerInstallment = $noOfInstallments > 0 ? $investment->installments->first()->total_amount : 0;
        $totalRent = $noOfInstallments > 0 ? $investment->installments->sum('rent') : 0;
        
        // Determine payment type and investment years from installments
        $paymentType = 'monthly'; // default
        $investmentYears = $investment->term_months / 12;
        
        if ($noOfInstallments > 1) {
            $firstDate = $investment->installments->first()->schedule_date;
            $secondDate = $investment->installments->skip(1)->first()->schedule_date;
            $diff = Carbon::parse($firstDate)->diffInMonths(Carbon::parse($secondDate));
            
            if ($diff == 1) {
                $paymentType = 'monthly';
            } elseif ($diff == 3) {
                $paymentType = 'quarterly';
            } elseif ($diff == 12) {
                $paymentType = 'yearly';
            }
        }
        
        return view('content.investments.edit', compact(
            'investment', 
            'members', 
            'investmentTypes',
            'noOfInstallments',
            'principalPerInstallment',
            'rentPerInstallment',
            'totalAmountPerInstallment',
            'totalRent',
            'paymentType',
            'investmentYears'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Investment $investment)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required|exists:members,id',
            'principal_amount' => 'required|numeric|min:0',
            'investment_type_id' => 'nullable|exists:investment_types,id',
            'start_date' => 'required|date',
            'interest_rate' => 'required|numeric',
            'investment_years' => 'required|integer|min:1',
            'payment_type' => 'required|in:monthly,quarterly,yearly,daily',
            'status' => 'required|in:active,matured,closed',
            'account_opening_date' => 'nullable|date',
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

            // Check if rate has changed
            $oldRate = $investment->rate;
            $newRate = $request->interest_rate;
            // Convert percentage to decimal if needed
            if ($newRate > 1) {
                $newRate = $newRate / 100;
            }
            $rateChanged = $oldRate != $newRate;

            // Calculate expiry date based on investment years
            $startDate = Carbon::parse($request->start_date);
            $expiryDate = $startDate->copy()->addYears((int) $request->investment_years);
            $termMonths = $request->investment_years * 12;

            // Determine rate_period and frequency
            $ratePeriod = 'annual';
            $frequency = $request->payment_type;

            $investment->update([
                'member_id' => $request->member_id,
                'principal_amount' => $request->principal_amount,
                'start_date' => $request->start_date,
                'term_months' => $termMonths,
                'expiry_date' => $expiryDate,
                'rate' => $newRate,
                'rate_period' => $ratePeriod,
                'frequency' => $frequency,
                'status' => $request->status,
                'notes' => $request->notes
            ]);

            // Update investment account if exists
            if ($investment->account) {
                $investment->account->update([
                    'account_opening_date' => $request->account_opening_date ?? $request->start_date,
                    'account_closing_date' => $request->gestation_maturity_date,
                    'account_notes' => $request->notes,
                    'updated_by' => auth()->id()
                ]);
            }

            // Create rate history entry if rate changed
            if ($rateChanged) {
                \App\Models\RateHistory::create([
                    'investment_id' => $investment->id,
                    'old_rate' => $oldRate,
                    'new_rate' => $newRate,
                    'effective_date' => now()->toDateString(),
                    'reason' => 'Rate updated via investment edit',
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment updated successfully',
                    'data' => $investment->load(['member', 'account', 'installments']),
                    'redirect' => route('investments.show', $investment)
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
            DB::beginTransaction();
            
            // Check if investment has paid installments
            $paidInstallments = $investment->installments()->where('status', 'paid')->count();
            if ($paidInstallments > 0) {
                if (request()->expectsJson() || request()->ajax()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete investment with paid installments. Please close the investment instead.'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Cannot delete investment with paid installments.');
            }

            // Check if investment has ledger entries (more than just the initial principal entry)
            $ledgerEntriesCount = $investment->ledgerEntries()->count();
            if ($ledgerEntriesCount > 1) {
                if (request()->expectsJson() || request()->ajax()) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete investment with existing transactions'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Cannot delete investment with existing transactions.');
            }

            // Delete related records
            $investment->installments()->delete();
            if ($investment->account) {
                $investment->account->accountNumberRecord()->delete();
                $investment->account->delete();
            }
            $investment->ledgerEntries()->delete();
            $investment->rateHistories()->delete();
            
            // Delete the investment
            $investment->delete();

            DB::commit();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment deleted successfully'
                ]);
            }

            return redirect()->route('investments.view-investments')
                ->with('success', 'Investment deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson() || request()->ajax()) {
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
            // First installment is on start_date, subsequent ones are calculated from start_date
            $scheduleDate = $startDate->copy();
            
            switch ($paymentType) {
                case 'monthly':
                    // First installment: start_date, second: start_date + 1 month, etc.
                    if ($i > 1) {
                        $scheduleDate->addMonths((int)($i - 1));
                    }
                    break;
                case 'quarterly':
                    // First installment: start_date, second: start_date + 3 months, etc.
                    if ($i > 1) {
                        $scheduleDate->addMonths((int)(($i - 1) * 3));
                    }
                    break;
                case 'yearly':
                    // First installment: start_date, second: start_date + 1 year, etc.
                    if ($i > 1) {
                        $scheduleDate->addYears((int)($i - 1));
                    }
                    break;
                case 'daily':
                    // First installment: start_date, second: start_date + 1 day, etc.
                    if ($i > 1) {
                        $scheduleDate->addDays((int)($i - 1));
                    }
                    break;
                default:
                    // Default to monthly
                    if ($i > 1) {
                        $scheduleDate->addMonths((int)($i - 1));
                    }
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
            'account_opening_date' => $request->account_opening_date,
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
