<?php

namespace App\Http\Controllers;

use App\Http\Requests\Investment\CalculateInvestmentRequest;
use App\Http\Requests\Investment\StoreInvestmentRequest;
use App\Models\Investment;
use App\Models\Member;
use App\Models\InvestmentType;
use App\Models\InvestmentAccountNumber;
use App\Models\RateHistory;
use App\Services\Investment\InvestmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use InvalidArgumentException;

class InvestmentController extends Controller
{
    public function __construct(
        private readonly InvestmentService $investmentService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Investment::with(['member', 'investmentType', 'ledgerEntries' => function ($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }]);

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%'.$request->product_name.'%');
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
                'data' => $investments,
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
        $nextAccountNumber = 'INV'.Carbon::now()->year.'-'.str_pad($latest ? $latest->serial + 1 : 1, 6, '0', STR_PAD_LEFT);
        $investmentTypes = InvestmentType::query()->orderBy('investment_type_name')->get();

        return view('content.investments.create', compact('members', 'investmentTypes', 'nextAccountNumber'));
    }

    /**
     * AJAX calculation preview — all math on the server.
     */
    public function calculate(CalculateInvestmentRequest $request)
    {
        try {
            $result = $this->investmentService->calculate($request->validated());

            return response()->json([
                'success' => true,
                'summary' => $result->summary,
                'schedule' => $result->schedule,
                'totals' => $result->totals,
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Calculation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * Recalculates on the server; browser totals are ignored.
     */
    public function store(StoreInvestmentRequest $request)
    {
        try {
            $investment = $this->investmentService->create($request->validated());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment created successfully',
                    'data' => $investment,
                    'redirect' => route('investments.show', $investment),
                ], 201);
            }

            return redirect()->route('investments.show', $investment)
                ->with('success', 'Investment created successfully.');
        } catch (InvalidArgumentException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create investment: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create investment: '.$e->getMessage())
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
            'investmentType',
            'account.accountNumberRecord.user',
            'installments',
            'ledgerEntries.createdBy',
        ]);
        $ledgerEntries = $investment->ledgerEntries()->orderBy('entry_date', 'desc')->paginate(10);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'investment' => $investment,
                    'ledger_entries' => $ledgerEntries,
                ],
            ]);
        }

        return view('content.investments.show', compact('investment', 'ledgerEntries'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Investment $investment)
    {
        $investment->load(['account', 'installments', 'investmentType']);
        $members = Member::select('id', 'name', 'member_unique_id')->get();
        $investmentTypes = InvestmentType::query()->orderBy('investment_type_name')->get();

        $noOfInstallments = $investment->installments->count();
        $principalPerInstallment = $noOfInstallments > 0 ? $investment->installments->first()->principal_amount : 0;
        $rentPerInstallment = $noOfInstallments > 0 ? $investment->installments->first()->rent : 0;
        $totalAmountPerInstallment = $noOfInstallments > 0 ? $investment->installments->first()->total_amount : 0;
        $totalRent = $noOfInstallments > 0 ? $investment->installments->sum('rent') : 0;

        $paymentType = 'monthly';
        $investmentYears = $investment->term_months / 12;

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
            'calculation_method' => 'nullable|in:annuity,reducing',
            'start_date' => 'required|date',
            'interest_rate' => 'required|numeric',
            'investment_years' => 'required|integer|min:1',
            'payment_type' => 'required|in:monthly',
            'status' => 'required|in:active,matured,closed',
            'account_opening_date' => 'nullable|date',
            'gestation_maturity_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $oldRate = $investment->rate;
            $newRate = (float) $request->interest_rate;
            if ($newRate > 1) {
                $newRate = $newRate / 100;
            }
            $rateChanged = (float) $oldRate != $newRate;

            $startDate = Carbon::parse($request->start_date);
            $expiryDate = $startDate->copy()->addYears((int) $request->investment_years);
            $termMonths = $request->investment_years * 12;

            $type = $request->filled('investment_type_id')
                ? InvestmentType::find($request->investment_type_id)
                : null;

            $investment->update([
                'member_id' => $request->member_id,
                'investment_type_id' => $request->investment_type_id ?? $investment->investment_type_id,
                'principal_amount' => $request->principal_amount,
                'product_name' => $type?->investment_type_name ?? $investment->product_name,
                'calculation_method' => $type && $type->isHpsm()
                    ? $request->calculation_method
                    : null,
                'start_date' => $request->start_date,
                'account_opening_date' => $request->account_opening_date ?? $request->start_date,
                'gestation_date' => $request->gestation_maturity_date,
                'term_months' => $termMonths,
                'expiry_date' => $expiryDate,
                'rate' => $newRate,
                'rate_period' => 'annual',
                'frequency' => 'monthly',
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            if ($investment->account) {
                $investment->account->update([
                    'account_opening_date' => $request->account_opening_date ?? $request->start_date,
                    'account_closing_date' => $request->gestation_maturity_date,
                    'account_notes' => $request->notes,
                    'updated_by' => auth()->id(),
                ]);
            }

            if ($rateChanged) {
                RateHistory::create([
                    'investment_id' => $investment->id,
                    'old_rate' => $oldRate,
                    'new_rate' => $newRate,
                    'effective_date' => now()->toDateString(),
                    'reason' => 'Rate updated via investment edit',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment updated successfully',
                    'data' => $investment->load(['member', 'account', 'installments']),
                    'redirect' => route('investments.show', $investment),
                ]);
            }

            return redirect()->route('investments.show', $investment)
                ->with('success', 'Investment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update investment: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update investment: '.$e->getMessage())
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

            $paidInstallments = $investment->installments()->where('status', 'paid')->count();
            if ($paidInstallments > 0) {
                DB::rollBack();
                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete investment with paid installments. Please close the investment instead.',
                    ], 422);
                }

                return redirect()->back()->with('error', 'Cannot delete investment with paid installments.');
            }

            $ledgerEntriesCount = $investment->ledgerEntries()->count();
            if ($ledgerEntriesCount > 2) {
                DB::rollBack();
                if (request()->expectsJson() || request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete investment with existing transactions',
                    ], 422);
                }

                return redirect()->back()->with('error', 'Cannot delete investment with existing transactions.');
            }

            $investment->installments()->delete();
            if ($investment->account) {
                $investment->account->accountNumberRecord()->delete();
                $investment->account->delete();
            }
            $investment->ledgerEntries()->delete();
            $investment->rateHistories()->delete();
            $investment->delete();

            DB::commit();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Investment deleted successfully',
                ]);
            }

            return redirect()->route('investments.view-investments')
                ->with('success', 'Investment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete investment: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete investment: '.$e->getMessage());
        }
    }

    /**
     * Get investments for a specific member.
     */
    public function getByMember($memberId)
    {
        $investments = Investment::with(['member', 'investmentType', 'ledgerEntries' => function ($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }])
            ->where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $investments,
        ]);
    }
}
