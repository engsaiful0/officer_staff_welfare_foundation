<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Member;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepositReportController extends Controller
{
    use DepositReportControllerMethods;
    /**
     * Display the reports index page
     */
    public function index()
    {
        $stats = $this->getDashboardStats();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        }

        return view('deposits.reports.index', compact('stats'));
    }

    /**
     * Portfolio report - overview of all deposits
     */
    public function portfolioReport(Request $request)
    {
        $query = Deposit::with(['member', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }]);

        // Apply filters
        if ($request->filled('member_id')) {
            $query->where('member_id', $request->member_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('deposit_type')) {
            $query->where('deposit_type', $request->deposit_type);
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $deposits = $query->orderBy('created_at', 'desc')->get();

        // Calculate summary statistics
        $summary = [
            'total_deposits' => $deposits->count(),
            'total_amount' => $deposits->sum('deposit_amount'),
            'total_balance' => $deposits->sum('current_balance'),
            'total_interest_accrued' => $deposits->sum('total_interest_accrued'),
            'active_deposits' => $deposits->where('status', 'active')->count(),
            'matured_deposits' => $deposits->where('status', 'matured')->count(),
            'closed_deposits' => $deposits->where('status', 'closed')->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'deposits' => $deposits,
                    'summary' => $summary
                ]
            ]);
        }

        $members = Member::select('id', 'name', 'member_unique_id')->get();
        
        return view('deposits.reports.portfolio', compact('deposits', 'summary', 'members'));
    }

    /**
     * Interest report - detailed interest tracking
     */
    public function interestReport(Request $request)
    {
        $query = LedgerEntry::where('entity_type', 'deposit')
            ->whereIn('type', ['accrual', 'interest'])
            ->with(['entity.member', 'createdBy']);

        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }

        if ($request->filled('member_id')) {
            $query->whereHas('entity', function($q) use ($request) {
                $q->where('member_id', $request->member_id);
            });
        }

        $interestEntries = $query->orderBy('entry_date', 'desc')->get();

        // Group by deposit for summary
        $depositSummary = $interestEntries->groupBy('entity_id')->map(function($entries) {
            $deposit = $entries->first()->entity;
            return [
                'deposit_id' => $deposit->id,
                'member_name' => $deposit->member->name,
                'deposit_type' => $deposit->deposit_type,
                'rate' => $deposit->rate_percentage,
                'total_interest' => $entries->sum('amount'),
                'entries_count' => $entries->count(),
                'last_accrual' => $entries->max('entry_date')
            ];
        });

        $summary = [
            'total_interest' => $interestEntries->sum('amount'),
            'total_entries' => $interestEntries->count(),
            'unique_deposits' => $depositSummary->count(),
            'average_interest_per_entry' => $interestEntries->avg('amount')
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'interest_entries' => $interestEntries,
                    'deposit_summary' => $depositSummary,
                    'summary' => $summary
                ]
            ]);
        }

        $members = Member::select('id', 'name', 'member_unique_id')->get();
        
        return view('deposits.reports.interest', compact('interestEntries', 'depositSummary', 'summary', 'members'));
    }

    /**
     * Maturity report - deposits approaching or past maturity
     */
    public function maturityReport(Request $request)
    {
        $daysAhead = $request->get('days_ahead', 30);
        $includePast = $request->get('include_past', true);

        $query = Deposit::with(['member'])
            ->whereNotNull('maturity_date');

        if ($includePast) {
            $query->where('maturity_date', '<=', Carbon::now()->addDays($daysAhead));
        } else {
            $query->whereBetween('maturity_date', [Carbon::now(), Carbon::now()->addDays($daysAhead)]);
        }

        $deposits = $query->orderBy('maturity_date', 'asc')->get();

        // Categorize by maturity status
        $categorized = [
            'overdue' => $deposits->filter(function($deposit) {
                return $deposit->maturity_date < Carbon::now();
            }),
            'due_soon' => $deposits->filter(function($deposit) {
                return $deposit->maturity_date >= Carbon::now() && 
                       $deposit->maturity_date <= Carbon::now()->addDays(30);
            }),
            'future' => $deposits->filter(function($deposit) {
                return $deposit->maturity_date > Carbon::now()->addDays(30);
            })
        ];

        $summary = [
            'total_deposits' => $deposits->count(),
            'overdue_count' => $categorized['overdue']->count(),
            'due_soon_count' => $categorized['due_soon']->count(),
            'future_count' => $categorized['future']->count(),
            'total_maturity_value' => $deposits->sum('current_balance')
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'deposits' => $deposits,
                    'categorized' => $categorized,
                    'summary' => $summary
                ]
            ]);
        }

        return view('deposits.reports.maturity', compact('deposits', 'categorized', 'summary'));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $totalDeposits = Deposit::count();
        $activeDeposits = Deposit::active()->count();
        $totalDepositAmount = Deposit::sum('deposit_amount');
        $totalCurrentBalance = Deposit::sum(DB::raw('(
            SELECT balance_after 
            FROM ledger_entries 
            WHERE entity_type = "deposit" 
            AND entity_id = deposits.id 
            ORDER BY entry_date DESC, created_at DESC 
            LIMIT 1
        )'));

        $totalInterestAccrued = LedgerEntry::where('entity_type', 'deposit')
            ->whereIn('type', ['accrual', 'interest'])
            ->sum('amount');

        $maturedDeposits = Deposit::where('maturity_date', '<=', Carbon::now())->count();
        $overdueDeposits = Deposit::where('maturity_date', '<', Carbon::now())
            ->where('status', 'active')
            ->count();

        return [
            'total_deposits' => $totalDeposits,
            'active_deposits' => $activeDeposits,
            'total_deposit_amount' => $totalDepositAmount,
            'total_current_balance' => $totalCurrentBalance,
            'total_interest_accrued' => $totalInterestAccrued,
            'matured_deposits' => $maturedDeposits,
            'overdue_deposits' => $overdueDeposits
        ];
    }
}
