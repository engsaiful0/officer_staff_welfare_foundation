<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AcademicYear;
use App\Services\FeeManagementService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentFinalReportExport;

class StudentFinalReportController extends Controller
{
    protected $feeManagementService;

    public function __construct(FeeManagementService $feeManagementService)
    {
        $this->feeManagementService = $feeManagementService;
    }

    /**
     * Generate final completion report for a student
     */
    public function generateFinalReport($studentId, Request $request)
    {
        $student = Student::with(['academicYear', 'technology', 'semester'])->findOrFail($studentId);
        $academicYearId = $request->get('academic_year_id', $student->academic_year_id);
        
        $report = $this->feeManagementService->getStudentFeeReport($studentId, $academicYearId);
        
        if (!$report) {
            return redirect()->back()->with('error', 'No fee data found for this student.');
        }

        // Check if student has completed all requirements
        $summary = $report['fee_summary'];
        $isComplete = $summary->all_fees_paid && 
                     $summary->semesters_completed >= 8 && 
                     $summary->months_completed >= 48;

        return view('content.final-report.student-final-report', compact('report', 'student', 'isComplete'));
    }

    /**
     * Generate completion certificate
     */
    public function generateCompletionCertificate($studentId, Request $request)
    {
        $student = Student::with(['academicYear', 'technology', 'semester'])->findOrFail($studentId);
        $academicYearId = $request->get('academic_year_id', $student->academic_year_id);
        
        $report = $this->feeManagementService->getStudentFeeReport($studentId, $academicYearId);
        
        if (!$report || !$report['fee_summary']->all_fees_paid) {
            return redirect()->back()->with('error', 'Student has not completed all fee payments. Cannot generate completion certificate.');
        }

        $pdf = Pdf::loadView('content.final-report.completion-certificate', compact('report', 'student'));
        return $pdf->stream('completion-certificate-' . $student->student_unique_id . '.pdf');
    }

    /**
     * Get all completed students
     */
    public function getCompletedStudents(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        
        $completedStudents = $this->feeManagementService->getAllStudentsFeeSummary($academicYearId)
            ->where('all_fees_paid', true)
            ->where('semesters_completed', '>=', 8)
            ->where('months_completed', '>=', 48);

        return response()->json([
            'students' => $completedStudents,
            'total' => $completedStudents->count()
        ]);
    }

    /**
     * Bulk generate completion certificates
     */
    public function bulkGenerateCompletionCertificates(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        $studentIds = $request->student_ids;
        $academicYearId = $request->get('academic_year_id');
        $certificates = [];

        foreach ($studentIds as $studentId) {
            $student = Student::with(['academicYear', 'technology'])->find($studentId);
            $report = $this->feeManagementService->getStudentFeeReport($studentId, $academicYearId);
            
            if ($report && $report['fee_summary']->all_fees_paid) {
                $certificates[] = [
                    'student' => $student,
                    'report' => $report
                ];
            }
        }

        if (empty($certificates)) {
            return redirect()->back()->with('error', 'No students have completed all fee payments.');
        }

        $pdf = Pdf::loadView('content.final-report.bulk-completion-certificates', compact('certificates'));
        return $pdf->download('bulk-completion-certificates.pdf');
    }

    /**
     * Export final reports to Excel
     */
    public function exportFinalReports(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        
        $completedStudents = $this->feeManagementService->getAllStudentsFeeSummary($academicYearId)
            ->where('all_fees_paid', true);

        return Excel::download(new StudentFinalReportExport($completedStudents), 'student-final-reports.xlsx');
    }

    /**
     * Get fee completion statistics
     */
    public function getCompletionStatistics(Request $request)
    {
        $academicYearId = $request->get('academic_year_id');
        $stats = $this->feeManagementService->getFeeCollectionStats($academicYearId);
        
        $completedStudents = $this->feeManagementService->getAllStudentsFeeSummary($academicYearId)
            ->where('all_fees_paid', true)
            ->where('semesters_completed', '>=', 8)
            ->where('months_completed', '>=', 48);
        
        $stats['fully_completed_students'] = $completedStudents->count();
        $stats['completion_rate'] = $stats['total_students'] > 0 
            ? round(($stats['fully_completed_students'] / $stats['total_students']) * 100, 2)
            : 0;
        
        return response()->json($stats);
    }
}
