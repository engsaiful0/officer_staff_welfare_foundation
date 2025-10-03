<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\LedgerEntry;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class InvestmentReportController extends Controller
{
    use InvestmentReportControllerMethods;
    /**
     * Display the reports index page
     */
    public function index()
    {
        $stats = $this->getDashboardStats();
        return view('content.investments.reports.index', compact('stats'));
    }

    /**
     * Portfolio Report - Overview of all investments
     */
    public function portfolioReport(Request $request)
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

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('start_date', '<=', $request->date_to);
        }

        $investments = $query->orderBy('created_at', 'desc')->get();

        // Calculate summary statistics
        $summary = [
            'total_investments' => $investments->count(),
            'total_principal' => $investments->sum('principal_amount'),
            'total_current_balance' => $investments->sum('current_balance'),
            'total_interest_accrued' => $investments->sum('total_interest_accrued'),
            'active_investments' => $investments->where('status', 'active')->count(),
            'matured_investments' => $investments->where('status', 'matured')->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'investments' => $investments,
                    'summary' => $summary
                ]
            ]);
        }

        $members = Member::select('id', 'name', 'member_unique_id')->get();
        return view('content.investments.reports.portfolio', compact('investments', 'summary', 'members'));
    }

    /**
     * Interest Report - Detailed interest accruals and payments
     */
    public function interestReport(Request $request)
    {
        $query = LedgerEntry::with(['investment.member', 'createdBy'])
            ->whereIn('type', ['accrual', 'payment']);

        // Apply filters
        if ($request->filled('investment_id')) {
            $query->where('investment_id', $request->investment_id);
        }

        if ($request->filled('member_id')) {
            $query->whereHas('investment', function($q) use ($request) {
                $q->where('member_id', $request->member_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('entry_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('entry_date', '<=', $request->date_to);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $entries = $query->orderBy('entry_date', 'desc')->get();

        // Calculate summary statistics
        $summary = [
            'total_accruals' => $entries->where('type', 'accrual')->sum('amount'),
            'total_payments' => $entries->where('type', 'payment')->sum('amount'),
            'net_interest' => $entries->where('type', 'accrual')->sum('amount') - $entries->where('type', 'payment')->sum('amount'),
            'accrual_count' => $entries->where('type', 'accrual')->count(),
            'payment_count' => $entries->where('type', 'payment')->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'entries' => $entries,
                    'summary' => $summary
                ]
            ]);
        }

        $investments = Investment::with('member')->get();
        $members = Member::select('id', 'name', 'member_unique_id')->get();
        
        return view('content.investments.reports.interest', compact('entries', 'summary', 'investments', 'members'));
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $today = Carbon::now()->toDateString();
        
        return [
            'total_investments' => Investment::count(),
            'active_investments' => Investment::where('status', 'active')->count(),
            'matured_investments' => Investment::where('status', 'matured')->count(),
            'total_principal' => Investment::sum('principal_amount'),
            'total_current_balance' => Investment::sum('current_balance'),
            'total_interest_accrued' => Investment::sum('total_interest_accrued'),
            'overdue_investments' => Investment::where('expiry_date', '<', $today)->where('status', 'active')->count(),
            'due_soon_investments' => Investment::whereBetween('expiry_date', [$today, Carbon::now()->addDays(30)->toDateString()])->count(),
        ];
    }
}
