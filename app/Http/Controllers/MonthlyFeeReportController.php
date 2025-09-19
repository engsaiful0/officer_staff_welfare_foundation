<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonthlyFeePayment;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\FeeSettings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MonthlyFeeReportController extends Controller
{
    /**
     * Display the monthly fee due report page
     */
    public function index()
    {
        $feeSettings = FeeSettings::getActive();
        
        // Get current month/year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        return view('content.fee-management.monthly-report', compact(
            'feeSettings',
            'currentMonth',
            'currentYear'
        ));
    }

    /**
     * Get monthly fee report data
     */
    public function getReportData(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'status' => 'nullable|in:all,paid,unpaid,overdue',
            'search' => 'nullable|string|max:255',
        ]);

        // Generate monthly fee payments for all students if they don't exist
        $this->ensureMonthlyPaymentsExist($request->month, $request->year);

        // Update overdue status for all unpaid payments before fetching data
        $this->updateOverdueStatusForReport($request->month, $request->year);

        $query = MonthlyFeePayment::with(['student', 'academicYear', 'feeCollect'])
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->whereNotExists(function ($subQuery) use ($request) {
                $subQuery->select(DB::raw(1))
                    ->from('student_monthly_fees')
                    ->whereColumn('student_monthly_fees.student_id', 'monthly_fee_payments.student_id')
                    ->where('student_monthly_fees.month_id', $request->month)
                    ->where('student_monthly_fees.is_paid', true);
            });

        // Apply status filter
        switch ($request->status) {
            case 'paid':
                $query->paid();
                break;
            case 'unpaid':
                $query->unpaid();
                break;
            case 'overdue':
                $query->overdue();
                break;
        }

        // Apply search filter
        if ($request->search) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('full_name_in_english_block_letter', 'like', '%' . $request->search . '%')
                  ->orWhere('student_unique_id', 'like', '%' . $request->search . '%');
            });
        }

        $payments = $query->orderBy('id', 'desc')->paginate(50);

        // Calculate statistics
        $stats = $this->calculateStatistics($request->month, $request->year);

        return response()->json([
            'success' => true,
            'data' => $payments,
            'stats' => $stats
        ]);
    }

    /**
     * Ensure monthly fee payments exist for all students for the given month/year
     */
    private function ensureMonthlyPaymentsExist($month, $year)
    {
        $feeSettings = FeeSettings::getActive();
        if (!$feeSettings) {
            return;
        }

        // Calculate due date
        $dueDate = Carbon::create($year, $month, $feeSettings->payment_deadline_day);

        // Get all students (regardless of academic year)
        $students = Student::all();

        foreach ($students as $student) {
            // Check if payment record already exists
            $existingPayment = MonthlyFeePayment::where([
                'student_id' => $student->id,
                'month' => $month,
                'year' => $year,
            ])->first();

            if (!$existingPayment) {
                MonthlyFeePayment::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $student->academic_year_id,
                    'month' => $month,
                    'year' => $year,
                    'fee_amount' => $feeSettings->amount,
                    'fine_amount' => 0.00,
                    'total_amount' => $feeSettings->amount,
                    'due_date' => $dueDate,
                    'is_paid' => false,
                    'is_overdue' => false,
                    'days_overdue' => 0,
                ]);
            } else {
                // Update existing payment with correct fee amount if it's 0 or null
                if ($existingPayment->fee_amount == 0 || $existingPayment->fee_amount == null) {
                    $existingPayment->update([
                        'fee_amount' => $feeSettings->amount,
                        'total_amount' => $feeSettings->amount + $existingPayment->fine_amount,
                    ]);
                }
            }
        }
    }

    /**
     * Update overdue status for payments in the report
     */
    private function updateOverdueStatusForReport($month, $year)
    {
        // Get all unpaid payments for the specified month/year
        $unpaidPayments = MonthlyFeePayment::where('month', $month)
            ->where('year', $year)
            ->where('is_paid', false)
            ->get();

        foreach ($unpaidPayments as $payment) {
            $payment->calculateAndUpdateOverdue();
        }
    }

    /**
     * Calculate report statistics
     */
    private function calculateStatistics($month, $year)
    {
        $baseQuery = MonthlyFeePayment::where('month', $month)
            ->where('year', $year)
            ->whereNotExists(function ($subQuery) use ($month) {
                $subQuery->select(DB::raw(1))
                    ->from('student_monthly_fees')
                    ->whereColumn('student_monthly_fees.student_id', 'monthly_fee_payments.student_id')
                    ->where('student_monthly_fees.month_id', $month)
                    ->where('student_monthly_fees.is_paid', true);
            });

        $totalStudents = (clone $baseQuery)->count();
        $paidCount = (clone $baseQuery)->paid()->count();
        $unpaidCount = (clone $baseQuery)->unpaid()->count();
        $overdueCount = (clone $baseQuery)->overdue()->count();

        $totalFeeAmount = (clone $baseQuery)->sum('fee_amount');
        $totalFineAmount = (clone $baseQuery)->sum('fine_amount');
        $totalCollected = (clone $baseQuery)->paid()->sum('total_amount');
        $totalPending = (clone $baseQuery)->unpaid()->sum('total_amount');

        return [
            'total_students' => $totalStudents,
            'paid_count' => $paidCount,
            'unpaid_count' => $unpaidCount,
            'overdue_count' => $overdueCount,
            'total_fee_amount' => number_format($totalFeeAmount, 2),
            'total_fine_amount' => number_format($totalFineAmount, 2),
            'total_collected' => number_format($totalCollected, 2),
            'total_pending' => number_format($totalPending, 2),
            'collection_rate' => $totalStudents > 0 ? round(($paidCount / $totalStudents) * 100, 2) : 0,
        ];
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'status' => 'nullable|in:all,paid,unpaid,overdue',
        ]);

        // Ensure monthly payments exist
        $this->ensureMonthlyPaymentsExist($request->month, $request->year);

        // Update overdue status before exporting
        $this->updateOverdueStatusForReport($request->month, $request->year);

        $query = MonthlyFeePayment::with(['student', 'academicYear'])
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->whereNotExists(function ($subQuery) use ($request) {
                $subQuery->select(DB::raw(1))
                    ->from('student_monthly_fees')
                    ->whereColumn('student_monthly_fees.student_id', 'monthly_fee_payments.student_id')
                    ->where('student_monthly_fees.month_id', $request->month)
                    ->where('student_monthly_fees.is_paid', true);
            });

        // Apply status filter
        switch ($request->status) {
            case 'paid':
                $query->paid();
                break;
            case 'unpaid':
                $query->unpaid();
                break;
            case 'overdue':
                $query->overdue();
                break;
        }

        $payments = $query->orderBy('id', 'desc')->get();
        $stats = $this->calculateStatistics($request->month, $request->year);
        $monthName = Carbon::create($request->year, $request->month, 1)->format('F');

        $pdf = Pdf::loadView('content.fee-management.pdf.monthly-report', compact(
            'payments', 
            'stats', 
            'monthName', 
            'request'
        ));

        $filename = "monthly-fee-report-{$monthName}-{$request->year}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'status' => 'nullable|in:all,paid,unpaid,overdue',
        ]);

        // Ensure monthly payments exist
        $this->ensureMonthlyPaymentsExist($request->month, $request->year);

        // Update overdue status before exporting
        $this->updateOverdueStatusForReport($request->month, $request->year);

        $query = MonthlyFeePayment::with(['student', 'academicYear'])
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->whereNotExists(function ($subQuery) use ($request) {
                $subQuery->select(DB::raw(1))
                    ->from('student_monthly_fees')
                    ->whereColumn('student_monthly_fees.student_id', 'monthly_fee_payments.student_id')
                    ->where('student_monthly_fees.month_id', $request->month)
                    ->where('student_monthly_fees.is_paid', true);
            });

        // Apply status filter
        switch ($request->status) {
            case 'paid':
                $query->paid();
                break;
            case 'unpaid':
                $query->unpaid();
                break;
            case 'overdue':
                $query->overdue();
                break;
        }

        $payments = $query->orderBy('id', 'desc')->get();
        $monthName = Carbon::create($request->year, $request->month, 1)->format('F');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'SL No',
            'Student ID',
            'Student Name',
            'Month',
            'Year',
            'Fee Amount',
            'Fine Amount',
            'Total Amount',
            'Due Date',
            'Payment Date',
            'Status',
            'Days Overdue',
            'Notes'
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Add data
        $row = 2;
        foreach ($payments as $index => $payment) {
            $sheet->fromArray([
                $index + 1,
                $payment->student->student_unique_id ?? '',
                $payment->student->full_name_in_english_block_letter ?? '',
                $payment->month_name,
                $payment->year,
                $payment->fee_amount,
                $payment->fine_amount,
                $payment->total_amount,
                $payment->due_date->format('Y-m-d'),
                $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '',
                $payment->is_paid ? 'Paid' : ($payment->is_overdue ? 'Overdue' : 'Pending'),
                $payment->days_overdue,
                $payment->notes ?? ''
            ], null, "A{$row}");
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "monthly-fee-report-{$monthName}-{$request->year}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Get summary statistics for dashboard
     */
    public function getDashboardStats(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Ensure monthly payments exist for current month
        $this->ensureMonthlyPaymentsExist($currentMonth, $currentYear);

        $stats = $this->calculateStatistics($currentMonth, $currentYear);
        
        // Get overdue payments
        $overduePayments = MonthlyFeePayment::with('student')
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->whereNotExists(function ($subQuery) use ($currentMonth) {
                $subQuery->select(DB::raw(1))
                    ->from('student_monthly_fees')
                    ->whereColumn('student_monthly_fees.student_id', 'monthly_fee_payments.student_id')
                    ->where('student_monthly_fees.month_id', $currentMonth)
                    ->where('student_monthly_fees.is_paid', true);
            })
            ->overdue()
            ->orderBy('days_overdue', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'overdue_payments' => $overduePayments,
            'current_month' => Carbon::now()->format('F Y')
        ]);
    }

    /**
     * Bulk update payment status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:monthly_fee_payments,id',
            'action' => 'required|in:mark_paid,mark_unpaid',
            'payment_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $payments = MonthlyFeePayment::whereIn('id', $request->payment_ids)->get();
            $updatedCount = 0;

            foreach ($payments as $payment) {
                if ($request->action === 'mark_paid') {
                    $payment->markAsPaid(null, $request->payment_date);
                } else {
                    $payment->update([
                        'is_paid' => false,
                        'payment_date' => null,
                        'fee_collect_id' => null,
                    ]);
                }
                $updatedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} payment records!"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating payments: ' . $e->getMessage()
            ], 500);
        }
    }
}
