<?php

namespace App\Http\Controllers;

use App\Exports\MemberDeductionsExport;
use App\Models\DepositInstallmentAmount;
use App\Models\InvestmentInstallment;
use App\Models\Member;
use App\Models\MemberDeduction;
use App\Models\Quard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class MemberDeductionController extends Controller
{
    /**
     * List saved deductions (monthly list / view).
     */
    public function index(Request $request)
    {
        $deductions = $this->buildDeductionListQuery($request)
            ->paginate(20)
            ->withQueryString();

        $members = Member::orderBy('name')->get(['id', 'name', 'member_unique_id']);

        return view('content.deductions.index', [
            'deductions' => $deductions,
            'members' => $members,
            'resolveAccountNumber' => $this->accountNumberResolver(),
        ]);
    }

    /**
     * Export filtered deductions to Excel.
     */
    public function exportExcel(Request $request)
    {
        $this->ensureDeductionListAccess();

        $deductions = $this->buildDeductionListQuery($request)->get();
        $filename = 'monthly-deductions-'.date('Y-m-d_His').'.xlsx';

        return Excel::download(
            new MemberDeductionsExport($deductions, $this->accountNumberResolver()),
            $filename
        );
    }

    /**
     * Printable view of filtered deductions.
     */
    public function exportPrint(Request $request)
    {
        $this->ensureDeductionListAccess();

        $deductions = $this->buildDeductionListQuery($request)->get();
        $resolveAccountNumber = $this->accountNumberResolver();
        $filterSummary = $this->buildFilterSummary($request);
        $summary = [
            'count' => $deductions->count(),
            'total_amount' => (float) $deductions->sum('total_amount'),
        ];

        return view('content.deductions.export-print', compact(
            'deductions',
            'summary',
            'filterSummary',
            'resolveAccountNumber'
        ));
    }

    /**
     * Add deduction form.
     */
    public function create()
    {
        $members = Member::orderBy('name')->get(['id', 'name', 'member_unique_id']);

        return view('content.deductions.create', compact('members'));
    }

    /**
     * Edit deduction form.
     */
    public function edit(MemberDeduction $member_deduction)
    {
        $member_deduction->load('member:id,name,member_unique_id');

        return view('content.deductions.edit', ['deduction' => $member_deduction]);
    }

    /**
     * Auto-fill amounts from deposit installments, investments, and quards for member + month + year.
     */
    public function calculateAmounts(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $payload = $this->computeAmountsForMember(
            (int) $request->member_id,
            (int) $request->month,
            (int) $request->year
        );

        return response()->json($payload);
    }

    /**
     * Create or update deduction rows for every active member for the given month/year.
     */
    public function generateMonthly(Request $request)
    {
        if ($request->input('deduction_date') === '' || $request->input('deduction_date') === null) {
            $request->merge(['deduction_date' => null]);
        }

        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'deduction_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:5000',
        ]);

        $month = (int) $validated['month'];
        $year = (int) $validated['year'];
        $deductionDate = $validated['deduction_date']
            ?? Carbon::create($year, $month, 1)->toDateString();
        $remarks = $validated['remarks'] ?? null;

        $members = Member::activeForDeductions()
            ->orderBy('name')
            ->get(['id']);

        if ($members->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active members found (no active deposit, investment, or qard).',
                'processed' => 0,
            ], 422);
        }

        $user = Auth::user();
        $userId = $user ? $user->id : null;
        $processed = 0;

        DB::transaction(function () use ($members, $month, $year, $deductionDate, $remarks, $userId, &$processed) {
            foreach ($members as $member) {
                $amounts = $this->computeAmountsForMember($member->id, $month, $year);
                MemberDeduction::updateOrCreate(
                    [
                        'member_id' => $member->id,
                        'month' => $month,
                        'year' => $year,
                    ],
                    array_merge($amounts, [
                        'deduction_date' => $deductionDate,
                        'remarks' => $remarks,
                        'user_id' => $userId,
                    ])
                );
                $processed++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Generated {$processed} deduction record(s) for ".date('F', mktime(0, 0, 0, $month, 1))." {$year}.",
            'processed' => $processed,
        ]);
    }

    /**
     * Store a new member deduction (AJAX).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => [
                'required',
                'exists:members,id',
                Rule::unique('member_deductions', 'member_id')->where(function ($query) use ($request) {
                    return $query->where('month', $request->month)->where('year', $request->year);
                }),
            ],
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'monthly_deposit_amount' => 'required|numeric|min:0',
            'monthly_investment_amount' => 'required|numeric|min:0',
            'monthly_qard_amount' => 'required|numeric|min:0',
            'profit_on_deposit_amount' => 'required|numeric|min:0',
            'compensation_on_investment_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'deduction_date' => 'required|date',
            'remarks' => 'nullable|string|max:5000',
        ]);

        $user = Auth::user();

        $deduction = MemberDeduction::create([
            'member_id' => $validated['member_id'],
            'month' => $validated['month'],
            'year' => $validated['year'],
            'monthly_deposit_amount' => $validated['monthly_deposit_amount'],
            'monthly_investment_amount' => $validated['monthly_investment_amount'],
            'monthly_qard_amount' => $validated['monthly_qard_amount'],
            'profit_on_deposit_amount' => $validated['profit_on_deposit_amount'],
            'compensation_on_investment_amount' => $validated['compensation_on_investment_amount'],
            'total_amount' => $validated['total_amount'],
            'deduction_date' => $validated['deduction_date'],
            'remarks' => $validated['remarks'] ?? null,
            'user_id' => $user ? $user->id : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Deduction saved successfully.',
            'id' => $deduction->id,
        ], 201);
    }

    /**
     * Update a member deduction (AJAX).
     */
    public function update(Request $request, MemberDeduction $member_deduction)
    {
        $validated = $request->validate([
            'monthly_deposit_amount' => 'required|numeric|min:0',
            'monthly_investment_amount' => 'required|numeric|min:0',
            'monthly_qard_amount' => 'required|numeric|min:0',
            'profit_on_deposit_amount' => 'required|numeric|min:0',
            'compensation_on_investment_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'deduction_date' => 'required|date',
            'remarks' => 'nullable|string|max:5000',
        ]);

        $member_deduction->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Deduction updated successfully.',
        ]);
    }

    /**
     * Delete a member deduction (AJAX).
     */
    public function destroy(MemberDeduction $member_deduction)
    {
        $member_deduction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deduction deleted.',
        ]);
    }

    private function buildDeductionListQuery(Request $request)
    {
        $query = MemberDeduction::with([
            'member:'.$this->memberColumnsForList(),
            'member.designation:id,designation_name',
            'user:id,name',
        ])
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->orderBy('member_id');

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->integer('member_id'));
        }
        if ($request->filled('month')) {
            $query->where('month', $request->integer('month'));
        }
        if ($request->filled('year')) {
            $query->where('year', $request->integer('year'));
        }

        return $query;
    }

    private function memberColumnsForList(): string
    {
        $columns = ['id', 'name', 'member_unique_id', 'mobile', 'designation_id'];
        if (Schema::hasColumn('members', 'deposit_account_number')) {
            $columns[] = 'deposit_account_number';
        }
        if (Schema::hasColumn('members', 'diposit_account_number')) {
            $columns[] = 'diposit_account_number';
        }

        return implode(',', $columns);
    }

    private function ensureDeductionListAccess(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->hasPermissionTo('add-deduction') && ! $user->hasPermissionTo('view-deduction'))) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * @return \Closure(?Member): string
     */
    private function accountNumberResolver(): \Closure
    {
        return function (?Member $member): string {
            if (! $member) {
                return '—';
            }

            $accountNumber = $member->deposit_account_number ?? $member->diposit_account_number;

            return $accountNumber !== null && $accountNumber !== '' ? $accountNumber : '—';
        };
    }

    private function buildFilterSummary(Request $request): string
    {
        $parts = [];
        if ($request->filled('member_id')) {
            $member = Member::find($request->integer('member_id'));
            $parts[] = 'Member: '.($member?->name ?? $request->member_id);
        }
        if ($request->filled('month')) {
            $parts[] = 'Month: '.date('F', mktime(0, 0, 0, $request->integer('month'), 1));
        }
        if ($request->filled('year')) {
            $parts[] = 'Year: '.$request->year;
        }

        return $parts ? implode(' | ', $parts) : 'All records';
    }

    /**
     * @return array<string, float>
     */
    private function computeAmountsForMember(int $memberId, int $month, int $year): array
    {
        $depositRow = DepositInstallmentAmount::where('member_id', $memberId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();

        if (! $depositRow) {
            $depositRow = DepositInstallmentAmount::where('member_id', $memberId)
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->first();
        }

        $monthlyDeposit = $depositRow ? (float) $depositRow->installment_amount : 0.0;

        $installmentAgg = InvestmentInstallment::query()
            ->whereHas('investment', function ($q) use ($memberId) {
                $q->where('member_id', $memberId);
            })
            ->whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $month)
            ->selectRaw('COALESCE(SUM(principal_amount), 0) as principal_sum')
            ->selectRaw('COALESCE(SUM(rent), 0) as rent_sum')
            ->selectRaw('COALESCE(SUM(fine_amount), 0) as fine_sum')
            ->first();

        $monthlyInvestment = $installmentAgg ? (float) $installmentAgg->principal_sum : 0.0;
        $profitOnDeposit = $installmentAgg ? (float) $installmentAgg->rent_sum : 0.0;
        $compensationOnInvestment = $installmentAgg ? (float) $installmentAgg->fine_sum : 0.0;

        $monthlyQard = (float) Quard::where('member_id', $memberId)
            ->where('status', 'active')
            ->sum('installment_amount');

        $total = round(
            $monthlyDeposit + $monthlyInvestment + $monthlyQard + $profitOnDeposit + $compensationOnInvestment,
            2
        );

        return [
            'monthly_deposit_amount' => round($monthlyDeposit, 2),
            'monthly_investment_amount' => round($monthlyInvestment, 2),
            'monthly_qard_amount' => round($monthlyQard, 2),
            'profit_on_deposit_amount' => round($profitOnDeposit, 2),
            'compensation_on_investment_amount' => round($compensationOnInvestment, 2),
            'total_amount' => $total,
        ];
    }
}
