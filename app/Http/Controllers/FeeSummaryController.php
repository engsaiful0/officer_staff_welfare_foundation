<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AcademicYear;
use App\Services\FeeManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentFeeReportExport;

class FeeSummaryController extends Controller
{
    protected $feeManagementService;

    public function __construct(FeeManagementService $feeManagementService)
    {
        $this->feeManagementService = $feeManagementService;
    }

    /**
     * Display fee summary index page
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::all();
        $selectedAcademicYear = $request->get('academic_year_id');
        
        $feeSummaries = $this->feeManagementService->getAllStudentsFeeSummary($selectedAcademicYear);
        $stats = $this->feeManagementService->getFeeCollectionStats($selectedAcademicYear);

        return view('content.fee-summary.index', compact(
            'feeSummaries', 
            'academicYears', 
            'selectedAcademicYear', 
            'stats'
        ));
    }

    /**
     * Display individual student fee report
     */
    public function showStudentReport($studentId, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $report = $this->feeManagementService->getStudentFeeReport($studentId, $academicYearId);
        
        if (!$report) {
            return redirect()->back()->with('error', 'No fee data found for this student.');
        }

        return view('content.fee-summary.student-report', compact('report'));
    }

    /**
     * Print student fee report
     */
    public function printStudentReport($studentId, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $report = $this->feeManagementService->getStudentFeeReport($studentId, $academicYearId);
        
        if (!$report) {
            return redirect()->back()->with('error', 'No fee data found for this student.');
        }

        $pdf = Pdf::loadView('content.fee-summary.print-student-report', compact('report'));
        return $pdf->stream('student-fee-report-' . $studentId . '.pdf');
    }

    /**
     * Export student fee report to PDF
     */
    public function exportStudentReportPdf($studentId, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $report = $this->feeManagementService->getStudentFeeReport($studentId, $academicYearId);
        
        if (!$report) {
            return redirect()->back()->with('error', 'No fee data found for this student.');
        }

        $pdf = Pdf::loadView('content.fee-summary.export-student-report', compact('report'));
        return $pdf->download('student-fee-report-' . $report['student']->student_unique_id . '.pdf');
    }

    /**
     * Export student fee report to Excel
     */
    public function exportStudentReportExcel($studentId, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $report = $this->feeManagementService->getStudentFeeReport($studentId, $academicYearId);
        
        if (!$report) {
            return redirect()->back()->with('error', 'No fee data found for this student.');
        }

        return Excel::download(new StudentFeeReportExport($report), 'student-fee-report-' . $report['student']->student_unique_id . '.xlsx');
    }

    /**
     * Export all students fee summary to PDF
     */
    public function exportAllStudentsPdf(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $feeSummaries = $this->feeManagementService->getAllStudentsFeeSummary($academicYearId);
        $stats = $this->feeManagementService->getFeeCollectionStats($academicYearId);
        $academicYear = $academicYearId ? AcademicYear::find($academicYearId) : null;

        $pdf = Pdf::loadView('content.fee-summary.export-all-students', compact('feeSummaries', 'stats', 'academicYear'));
        return $pdf->download('all-students-fee-summary.pdf');
    }

    /**
     * Export all students fee summary to Excel
     */
    public function exportAllStudentsExcel(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $feeSummaries = $this->feeManagementService->getAllStudentsFeeSummary($academicYearId);
        $stats = $this->feeManagementService->getFeeCollectionStats($academicYearId);
        $academicYear = $academicYearId ? AcademicYear::find($academicYearId) : null;

        return Excel::download(new AllStudentsFeeSummaryExport($feeSummaries, $stats, $academicYear), 'all-students-fee-summary.xlsx');
    }

    /**
     * Get fee collection statistics
     */
    public function getStats(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $stats = $this->feeManagementService->getFeeCollectionStats($academicYearId);
        
        return response()->json($stats);
    }

    /**
     * Update fee summary for a specific student
     */
    public function updateStudentSummary($studentId, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        
        if (!$academicYearId) {
            return response()->json(['error' => 'Academic year is required'], 400);
        }

        $summary = $this->feeManagementService->updateFeeSummary($studentId, $academicYearId);
        
        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);
    }

    /**
     * Get detailed fee breakdown for a student
     */
    public function getStudentFeeBreakdown($studentId, Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $report = $this->feeManagementService->getStudentFeeReport($studentId, $academicYearId);
        
        if (!$report) {
            return response()->json(['error' => 'No fee data found'], 404);
        }

        return response()->json($report);
    }
}