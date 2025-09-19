<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentFeeSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'semester_fees_paid',
        'total_semester_fees',
        'paid_semester_fees',
        'monthly_fees_paid',
        'total_monthly_fees',
        'paid_monthly_fees',
        'total_fees',
        'total_paid',
        'total_due',
        'all_semester_fees_paid',
        'all_monthly_fees_paid',
        'all_fees_paid',
        'semesters_completed',
        'months_completed',
        'total_semesters',
        'total_months',
    ];

    protected $casts = [
        'semester_fees_paid' => 'array',
        'monthly_fees_paid' => 'array',
        'total_semester_fees' => 'decimal:2',
        'paid_semester_fees' => 'decimal:2',
        'total_monthly_fees' => 'decimal:2',
        'paid_monthly_fees' => 'decimal:2',
        'total_fees' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'total_due' => 'decimal:2',
        'all_semester_fees_paid' => 'boolean',
        'all_monthly_fees_paid' => 'boolean',
        'all_fees_paid' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Calculate and update fee summary for a student
     */
    public function calculateFeeSummary()
    {
        $studentId = $this->student_id;
        $academicYearId = $this->academic_year_id;

        // Get all fee collections for this student
        $feeCollections = FeeCollect::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->get();

        // Initialize arrays
        $paidSemesterIds = [];
        $paidMonthIds = [];
        $totalPaidAmount = 0;

        // Process each fee collection
        foreach ($feeCollections as $collection) {
            $feeHeads = is_string($collection->fee_heads) 
                ? json_decode($collection->fee_heads, true) 
                : $collection->fee_heads;

            if (is_array($feeHeads)) {
                foreach ($feeHeads as $feeHead) {
                    $feeHeadId = is_array($feeHead) ? $feeHead['id'] : $feeHead;
                    $feeHeadModel = FeeHead::find($feeHeadId);

                    if ($feeHeadModel) {
                        if ($feeHeadModel->fee_type === 'Regular') {
                            // Semester fee
                            if (!in_array($feeHeadModel->semester_id, $paidSemesterIds)) {
                                $paidSemesterIds[] = $feeHeadModel->semester_id;
                            }
                        } elseif ($feeHeadModel->fee_type === 'Monthly') {
                            // Monthly fee
                            if (isset($feeHead['months']) && is_array($feeHead['months'])) {
                                $paidMonthIds = array_merge($paidMonthIds, $feeHead['months']);
                            } elseif ($feeHeadModel->month_id) {
                                if (!in_array($feeHeadModel->month_id, $paidMonthIds)) {
                                    $paidMonthIds[] = $feeHeadModel->month_id;
                                }
                            }
                        }
                    }
                }
            }

            $totalPaidAmount += $collection->total_amount;
        }

        // Get all semester fees for this academic year
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

        // Calculate totals
        $totalSemesterFees = $allSemesterFees->sum('amount');
        $totalMonthlyFees = $allMonthlyFees->sum('amount') * 48; // 48 months over 4 years
        $totalFees = $totalSemesterFees + $totalMonthlyFees;

        // Calculate paid amounts
        $paidSemesterFees = $allSemesterFees->whereIn('semester_id', $paidSemesterIds)->sum('amount');
        $paidMonthlyFees = $allMonthlyFees->sum('amount') * count(array_unique($paidMonthIds));

        // Update the summary
        $this->update([
            'semester_fees_paid' => array_unique($paidSemesterIds),
            'monthly_fees_paid' => array_unique($paidMonthIds),
            'total_semester_fees' => $totalSemesterFees,
            'paid_semester_fees' => $paidSemesterFees,
            'total_monthly_fees' => $totalMonthlyFees,
            'paid_monthly_fees' => $paidMonthlyFees,
            'total_fees' => $totalFees,
            'total_paid' => $totalPaidAmount,
            'total_due' => $totalFees - $totalPaidAmount,
            'all_semester_fees_paid' => count($paidSemesterIds) >= 8,
            'all_monthly_fees_paid' => count(array_unique($paidMonthIds)) >= 48,
            'all_fees_paid' => $totalPaidAmount >= $totalFees,
            'semesters_completed' => count($paidSemesterIds),
            'months_completed' => count(array_unique($paidMonthIds)),
        ]);

        return $this;
    }

    /**
     * Get completion percentage
     */
    public function getCompletionPercentageAttribute()
    {
        if ($this->total_fees == 0) return 0;
        return round(($this->total_paid / $this->total_fees) * 100, 2);
    }

    /**
     * Get remaining semesters
     */
    public function getRemainingSemestersAttribute()
    {
        $allSemesters = [1, 2, 3, 4, 5, 6, 7, 8]; // Assuming semester IDs 1-8
        return array_diff($allSemesters, $this->semester_fees_paid ?? []);
    }

    /**
     * Get remaining months
     */
    public function getRemainingMonthsAttribute()
    {
        $allMonths = range(1, 48); // 48 months over 4 years
        return array_diff($allMonths, $this->monthly_fees_paid ?? []);
    }
}
