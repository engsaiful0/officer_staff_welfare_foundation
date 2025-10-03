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

trait InvestmentReportControllerMethods
{
    /**
     * Maturity Report - Investments approaching or past maturity
     */
    public function maturityReport(Request $request)
    {
        $today = Carbon::now()->toDateString();
        $daysAhead = $request->get('days_ahead', 30);
        $futureDate = Carbon::now()->addDays($daysAhead)->toDateString();

        $query = Investment::with(['member', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }]);

        // Filter by maturity status
        $maturityFilter = $request->get('maturity_filter', 'all');
        
        switch ($maturityFilter) {
            case 'overdue':
                $query->where('expiry_date', '<', $today);
                break;
            case 'due_soon':
                $query->whereBetween('expiry_date', [$today, $futureDate]);
                break;
            case 'matured':
                $query->where('expiry_date', '<=', $today);
                break;
            case 'active':
                $query->where('expiry_date', '>', $today)->where('status', 'active');
                break;
        }

        $investments = $query->orderBy('expiry_date')->get();

        // Calculate summary statistics
        $summary = [
            'total_investments' => $investments->count(),
            'overdue_count' => $investments->where('expiry_date', '<', $today)->count(),
            'due_soon_count' => $investments->whereBetween('expiry_date', [$today, $futureDate])->count(),
            'total_principal' => $investments->sum('principal_amount'),
            'total_current_balance' => $investments->sum('current_balance'),
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

        return view('content.investments.reports.maturity', compact('investments', 'summary', 'daysAhead'));
    }

    /**
     * Export portfolio report to PDF
     */
    public function exportPdf(Request $request)
    {
        $reportType = $request->get('report_type', 'portfolio');
        
        switch ($reportType) {
            case 'portfolio':
                $data = $this->getPortfolioReportData($request);
                $pdf = Pdf::loadView('content.investments.reports.pdf.portfolio', $data);
                break;
            case 'interest':
                $data = $this->getInterestReportData($request);
                $pdf = Pdf::loadView('content.investments.reports.pdf.interest', $data);
                break;
            case 'maturity':
                $data = $this->getMaturityReportData($request);
                $pdf = Pdf::loadView('content.investments.reports.pdf.maturity', $data);
                break;
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }

        $filename = "investment_{$reportType}_report_" . date('Y-m-d') . '.pdf';
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'download_url' => route('content.investments.reports.export-pdf', array_merge($request->all(), ['download' => true]))
            ]);
        }

        return $pdf->download($filename);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $reportType = $request->get('report_type', 'portfolio');
        
        switch ($reportType) {
            case 'portfolio':
                $data = $this->getPortfolioReportData($request);
                return Excel::download(new \App\Exports\InvestmentPortfolioExport($data), "portfolio_report_" . date('Y-m-d') . '.xlsx');
            case 'interest':
                $data = $this->getInterestReportData($request);
                return Excel::download(new \App\Exports\InvestmentInterestExport($data), "interest_report_" . date('Y-m-d') . '.xlsx');
            case 'maturity':
                $data = $this->getMaturityReportData($request);
                return Excel::download(new \App\Exports\InvestmentMaturityExport($data), "maturity_report_" . date('Y-m-d') . '.xlsx');
            default:
                return response()->json(['error' => 'Invalid report type'], 400);
        }
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

    /**
     * Get portfolio report data
     */
    private function getPortfolioReportData(Request $request)
    {
        $query = Investment::with(['member', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }]);

        // Apply same filters as portfolioReport method
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

        $summary = [
            'total_investments' => $investments->count(),
            'total_principal' => $investments->sum('principal_amount'),
            'total_current_balance' => $investments->sum('current_balance'),
            'total_interest_accrued' => $investments->sum('total_interest_accrued'),
            'active_investments' => $investments->where('status', 'active')->count(),
            'matured_investments' => $investments->where('status', 'matured')->count(),
        ];

        return compact('investments', 'summary');
    }

    /**
     * Get interest report data
     */
    private function getInterestReportData(Request $request)
    {
        $query = LedgerEntry::with(['investment.member', 'createdBy'])
            ->whereIn('type', ['accrual', 'payment']);

        // Apply same filters as interestReport method
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

        $summary = [
            'total_accruals' => $entries->where('type', 'accrual')->sum('amount'),
            'total_payments' => $entries->where('type', 'payment')->sum('amount'),
            'net_interest' => $entries->where('type', 'accrual')->sum('amount') - $entries->where('type', 'payment')->sum('amount'),
            'accrual_count' => $entries->where('type', 'accrual')->count(),
            'payment_count' => $entries->where('type', 'payment')->count(),
        ];

        return compact('entries', 'summary');
    }

    /**
     * Get maturity report data
     */
    private function getMaturityReportData(Request $request)
    {
        $today = Carbon::now()->toDateString();
        $daysAhead = $request->get('days_ahead', 30);
        $futureDate = Carbon::now()->addDays($daysAhead)->toDateString();

        $query = Investment::with(['member', 'ledgerEntries' => function($q) {
            $q->orderBy('entry_date', 'desc')->limit(1);
        }]);

        $maturityFilter = $request->get('maturity_filter', 'all');
        
        switch ($maturityFilter) {
            case 'overdue':
                $query->where('expiry_date', '<', $today);
                break;
            case 'due_soon':
                $query->whereBetween('expiry_date', [$today, $futureDate]);
                break;
            case 'matured':
                $query->where('expiry_date', '<=', $today);
                break;
            case 'active':
                $query->where('expiry_date', '>', $today)->where('status', 'active');
                break;
        }

        $investments = $query->orderBy('expiry_date')->get();

        $summary = [
            'total_investments' => $investments->count(),
            'overdue_count' => $investments->where('expiry_date', '<', $today)->count(),
            'due_soon_count' => $investments->whereBetween('expiry_date', [$today, $futureDate])->count(),
            'total_principal' => $investments->sum('principal_amount'),
            'total_current_balance' => $investments->sum('current_balance'),
        ];

        return compact('investments', 'summary', 'daysAhead');
    }
}
