<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentFeeSummary;
use App\Models\StudentSemesterFee;
use App\Models\StudentMonthlyFee;
use App\Models\FeeCollect;
use App\Models\FeeHead;
use App\Models\Semester;
use App\Models\Month;
use App\Models\FeeSettings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeeManagementService
{
    /**
     * Process fee collection and update tracking tables
     */
    public function processFeeCollection(FeeCollect $feeCollect)
    {
        DB::transaction(function () use ($feeCollect) {
            $feeHeads = is_string($feeCollect->fee_heads) 
                ? json_decode($feeCollect->fee_heads, true) 
                : $feeCollect->fee_heads;

            if (is_array($feeHeads)) {
                foreach ($feeHeads as $feeHeadData) {
                    $feeHeadId = is_array($feeHeadData) ? $feeHeadData['id'] : $feeHeadData;
                    $feeHead = FeeHead::find($feeHeadId);

                    if ($feeHead) {
                        if ($feeHead->fee_type === 'Regular') {
                            // Process semester fee
                            $this->processSemesterFee($feeCollect, $feeHead);
                        } elseif ($feeHead->fee_type === 'Monthly') {
                            // Process monthly fee
                            $this->processMonthlyFee($feeCollect, $feeHead, $feeHeadData);
                        }
                    }
                }
            }

            // Update fee summary
            $this->updateFeeSummary($feeCollect->student_id, $feeCollect->academic_year_id);
        });
    }

    /**
     * Process semester fee payment
     */
    private function processSemesterFee(FeeCollect $feeCollect, FeeHead $feeHead)
    {
        StudentSemesterFee::updateOrCreate(
            [
                'student_id' => $feeCollect->student_id,
                'academic_year_id' => $feeCollect->academic_year_id,
                'semester_id' => $feeHead->semester_id,
            ],
            [
                'fee_collect_id' => $feeCollect->id,
                'amount' => $feeHead->amount,
                'payment_date' => $feeCollect->date,
                'is_paid' => true,
                'notes' => 'Paid via fee collection #' . $feeCollect->id,
            ]
        );
    }

    /**
     * Process monthly fee payment
     */
    private function processMonthlyFee(FeeCollect $feeCollect, FeeHead $feeHead, array $feeHeadData)
    {
        $months = $feeHeadData['months'] ?? [$feeHead->month_id];
        
        foreach ($months as $monthId) {
            StudentMonthlyFee::updateOrCreate(
                [
                    'student_id' => $feeCollect->student_id,
                    'academic_year_id' => $feeCollect->academic_year_id,
                    'month_id' => $monthId,
                ],
                [
                    'fee_collect_id' => $feeCollect->id,
                    'amount' => $feeHead->amount,
                    'payment_date' => $feeCollect->date,
                    'is_paid' => true,
                    'notes' => 'Paid via fee collection #' . $feeCollect->id,
                ]
            );
        }
    }

    /**
     * Update or create fee summary for a student
     */
    public function updateFeeSummary($studentId, $academicYearId)
    {
        $summary = StudentFeeSummary::firstOrCreate(
            [
                'student_id' => $studentId,
                'academic_year_id' => $academicYearId,
            ]
        );

        // Get paid semester fees
        $paidSemesterFees = StudentSemesterFee::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('is_paid', true)
            ->get();

        // Get paid monthly fees
        $paidMonthlyFees = StudentMonthlyFee::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('is_paid', true)
            ->get();

        // Calculate totals
        $paidSemesterIds = $paidSemesterFees->pluck('semester_id')->unique()->toArray();
        $paidMonthIds = $paidMonthlyFees->pluck('month_id')->unique()->toArray();
        
        $totalPaidSemesterFees = $paidSemesterFees->sum('amount');
        $totalPaidMonthlyFees = $paidMonthlyFees->sum('amount');

        // Get all possible semester fees (8 semesters)
        $allSemesterFees = FeeHead::where('fee_type', 'Regular')
            ->whereIn('semester_id', function($query) {
                $query->select('id')
                    ->from('semesters')
                    ->whereIn('semester_name', [
                        '1st Semester', '2nd Semester', '3rd Semester', '4th Semester',
                        '5th Semester', '6th Semester', '7th Semester', '8th Semester'
                    ]);
            })
            ->get();

        // Get all monthly fees
        $allMonthlyFees = FeeHead::where('fee_type', 'Monthly')->get();

        $totalSemesterFees = $allSemesterFees->sum('amount');
        $totalMonthlyFees = $allMonthlyFees->sum('amount') * 48; // 48 months over 4 years
        $totalFees = $totalSemesterFees + $totalMonthlyFees;
        $totalPaid = $totalPaidSemesterFees + $totalPaidMonthlyFees;

        // Update summary
        $summary->update([
            'semester_fees_paid' => $paidSemesterIds,
            'monthly_fees_paid' => $paidMonthIds,
            'total_semester_fees' => $totalSemesterFees,
            'paid_semester_fees' => $totalPaidSemesterFees,
            'total_monthly_fees' => $totalMonthlyFees,
            'paid_monthly_fees' => $totalPaidMonthlyFees,
            'total_fees' => $totalFees,
            'total_paid' => $totalPaid,
            'total_due' => $totalFees - $totalPaid,
            'all_semester_fees_paid' => count($paidSemesterIds) >= 8,
            'all_monthly_fees_paid' => count($paidMonthIds) >= 48,
            'all_fees_paid' => $totalPaid >= $totalFees,
            'semesters_completed' => count($paidSemesterIds),
            'months_completed' => count($paidMonthIds),
        ]);

        return $summary;
    }

    /**
     * Get comprehensive fee report for a student
     */
    public function getStudentFeeReport($studentId, $academicYearId = null)
    {
        $student = Student::with(['academicYear', 'technology', 'semester'])->findOrFail($studentId);
        
        $query = StudentFeeSummary::where('student_id', $studentId);
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        $feeSummary = $query->first();
        
        if (!$feeSummary) {
            return null;
        }

        // Get detailed semester fees
        $semesterFees = StudentSemesterFee::with(['semester', 'feeCollect'])
            ->where('student_id', $studentId)
            ->where('academic_year_id', $feeSummary->academic_year_id)
            ->orderBy('semester_id')
            ->get();

        // Get detailed monthly fees
        $monthlyFees = StudentMonthlyFee::with(['month', 'feeCollect'])
            ->where('student_id', $studentId)
            ->where('academic_year_id', $feeSummary->academic_year_id)
            ->orderBy('month_id')
            ->get();

        // Get all fee collections
        $feeCollections = FeeCollect::with(['paymentMethod', 'user'])
            ->where('student_id', $studentId)
            ->where('academic_year_id', $feeSummary->academic_year_id)
            ->orderBy('date', 'desc')
            ->get();

        return [
            'student' => $student,
            'fee_summary' => $feeSummary,
            'semester_fees' => $semesterFees,
            'monthly_fees' => $monthlyFees,
            'fee_collections' => $feeCollections,
        ];
    }

    /**
     * Get all students with fee summary
     */
    public function getAllStudentsFeeSummary($academicYearId = null)
    {
        $query = StudentFeeSummary::with(['student.academicYear', 'student.technology', 'academicYear']);
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        return $query->orderBy('total_due', 'desc')->get();
    }

    /**
     * Generate fee collection statistics
     */
    public function getFeeCollectionStats($academicYearId = null)
    {
        $query = StudentFeeSummary::query();
        
        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }
        
        $summaries = $query->get();
        
        $totalStudents = $summaries->count();
        $studentsWithCompleteFees = $summaries->where('all_fees_paid', true)->count();
        $studentsWithPartialFees = $summaries->where('all_fees_paid', false)->where('total_paid', '>', 0)->count();
        $studentsWithNoFees = $summaries->where('total_paid', 0)->count();
        
        $totalExpectedFees = $summaries->sum('total_fees');
        $totalCollectedFees = $summaries->sum('total_paid');
        $totalDueFees = $summaries->sum('total_due');
        
        return [
            'total_students' => $totalStudents,
            'students_with_complete_fees' => $studentsWithCompleteFees,
            'students_with_partial_fees' => $studentsWithPartialFees,
            'students_with_no_fees' => $studentsWithNoFees,
            'total_expected_fees' => $totalExpectedFees,
            'total_collected_fees' => $totalCollectedFees,
            'total_due_fees' => $totalDueFees,
            'collection_percentage' => $totalExpectedFees > 0 ? round(($totalCollectedFees / $totalExpectedFees) * 100, 2) : 0,
        ];
    }

    /**
     * Calculate overdue fine for monthly fees
     */
    public function calculateOverdueFine($feeHeads, $months, $paymentDate)
    {
        $feeSettings = FeeSettings::getActive();
        if (!$feeSettings) {
            return [
                'total_fine_amount' => 0,
                'overdue_days' => 0,
                'fine_details' => []
            ];
        }

        $totalFineAmount = 0;
        $maxOverdueDays = 0;
        $fineDetails = [];

        foreach ($feeHeads as $feeHeadData) {
            $feeHeadId = is_array($feeHeadData) ? $feeHeadData['id'] : $feeHeadData;
            $feeHead = FeeHead::find($feeHeadId);

            if ($feeHead && $feeHead->fee_type === 'Monthly') {
                $feeHeadMonths = isset($feeHeadData['months']) ? $feeHeadData['months'] : [$feeHead->month_id];
                
                foreach ($feeHeadMonths as $monthId) {
                    $month = Month::find($monthId);
                    if ($month) {
                        $overdueInfo = $this->calculateMonthOverdue($month, $paymentDate, $feeSettings, $feeHead->amount);
                        
                        if ($overdueInfo['overdue_days'] > 0) {
                            $totalFineAmount += $overdueInfo['fine_amount'];
                            $maxOverdueDays = max($maxOverdueDays, $overdueInfo['overdue_days']);
                            
                            $fineDetails[] = [
                                'month_id' => $monthId,
                                'month_name' => $month->month_name,
                                'fee_head_id' => $feeHeadId,
                                'fee_head_name' => $feeHead->name,
                                'fee_amount' => $feeHead->amount,
                                'overdue_days' => $overdueInfo['overdue_days'],
                                'fine_amount' => $overdueInfo['fine_amount'],
                                'deadline_date' => $overdueInfo['deadline_date'],
                                'payment_date' => $paymentDate
                            ];
                        }
                    }
                }
            }
        }

        return [
            'total_fine_amount' => round($totalFineAmount, 2),
            'overdue_days' => $maxOverdueDays,
            'fine_details' => $fineDetails
        ];
    }

    /**
     * Calculate overdue for a specific month
     */
    private function calculateMonthOverdue($month, $paymentDate, $feeSettings, $feeAmount)
    {
        $paymentDateCarbon = Carbon::parse($paymentDate);
        $currentYear = $paymentDateCarbon->year;
        $currentMonth = $paymentDateCarbon->month;
        
        // For monthly fees, we need to determine which year the deadline should be calculated for
        // The logic should be:
        // 1. If we're in the same month, check if we're past the deadline day
        // 2. If we're in a later month, the deadline was earlier this year
        // 3. If we're in an earlier month, the deadline was last year
        // 4. Future months should never be overdue since their deadline hasn't occurred yet
        
        $deadlineYear = $currentYear;
        
        if ($month->id == $currentMonth) {
            // Same month - check if we're past the deadline day
            $deadlineDay = $feeSettings->payment_deadline_day;
            if ($paymentDateCarbon->day > $deadlineDay) {
                // We're past the deadline this month
                $deadlineYear = $currentYear;
            } else {
                // We're before the deadline this month, so no fine
                return [
                    'overdue_days' => 0,
                    'fine_amount' => 0,
                    'deadline_date' => Carbon::create($currentYear, $month->id, $deadlineDay)->format('Y-m-d')
                ];
            }
        } else if ($month->id < $currentMonth) {
            // Month has already passed this year, so deadline was this year
            $deadlineYear = $currentYear;
        } else {
            // Month hasn't occurred yet this year - this should never be overdue
            // Future months cannot be overdue since their deadline hasn't occurred yet
            return [
                'overdue_days' => 0,
                'fine_amount' => 0,
                'deadline_date' => Carbon::create($currentYear, $month->id, $feeSettings->payment_deadline_day)->format('Y-m-d')
            ];
        }
        
        // Calculate the deadline for this month in the appropriate year
        $deadlineDate = Carbon::create($deadlineYear, $month->id, $feeSettings->payment_deadline_day);
        
        // Calculate overdue days - if payment date is after deadline, it's overdue
        $overdueDays = 0;
        if ($paymentDateCarbon->gt($deadlineDate)) {
            $overdueDays = $deadlineDate->diffInDays($paymentDateCarbon);
        }
        
        if ($overdueDays <= 0) {
            return [
                'overdue_days' => 0,
                'fine_amount' => 0,
                'deadline_date' => $deadlineDate->format('Y-m-d')
            ];
        }

        // Calculate fine amount
        $fineAmount = $feeSettings->calculateFineAmount($overdueDays, $feeAmount);

        return [
            'overdue_days' => $overdueDays,
            'fine_amount' => $fineAmount,
            'deadline_date' => $deadlineDate->format('Y-m-d')
        ];
    }
}