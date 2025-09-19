<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MonthlyFeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'month',
        'year',
        'fee_amount',
        'fine_amount',
        'total_amount',
        'due_date',
        'payment_date',
        'is_paid',
        'is_overdue',
        'days_overdue',
        'fee_collect_id',
        'notes',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
        'is_paid' => 'boolean',
        'is_overdue' => 'boolean',
    ];

    protected $appends = ['month_name', 'status_badge'];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function feeCollect()
    {
        return $this->belongsTo(FeeCollect::class);
    }

    // Accessors
    public function getMonthNameAttribute()
    {
        return Carbon::create($this->year, $this->month, 1)->format('F');
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->is_paid) {
            return '<span class="badge bg-success">Paid</span>';
        } elseif ($this->is_overdue) {
            return '<span class="badge bg-danger">Overdue</span>';
        } else {
            return '<span class="badge bg-warning">Pending</span>';
        }
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_overdue', true);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    // Methods
    public function calculateAndUpdateOverdue()
    {
        $now = Carbon::now();
        $daysOverdue = $now->gt($this->due_date) ? $this->due_date->diffInDays($now) : 0;
        
        $this->days_overdue = $daysOverdue;
        $this->is_overdue = $daysOverdue > 0 && !$this->is_paid;
        
        if ($this->is_overdue && !$this->is_paid) {
            $feeSettings = FeeSettings::getActive();
            if ($feeSettings) {
                $this->fine_amount = $feeSettings->calculateFineAmount($daysOverdue, $this->fee_amount);
                $this->total_amount = $this->fee_amount + $this->fine_amount;
            }
        }
        
        $this->save();
        return $this;
    }

    public function markAsPaid($feeCollectId = null, $paymentDate = null)
    {
        $this->is_paid = true;
        $this->payment_date = $paymentDate ?? Carbon::now();
        $this->fee_collect_id = $feeCollectId;
        $this->save();
        
        return $this;
    }

    /**
     * Generate monthly fee payments for all students for a given month/year
     */
    public static function generateMonthlyPayments($month, $year, $academicYearId = null)
    {
        $feeSettings = FeeSettings::getActive();
        if (!$feeSettings) {
            throw new \Exception('No active fee settings found. Please configure fee settings first.');
        }

        // Get current academic year if not provided
        if (!$academicYearId) {
            $currentAcademicYear = AcademicYear::where('is_current', true)->first();
            if (!$currentAcademicYear) {
                throw new \Exception('No current academic year found.');
            }
            $academicYearId = $currentAcademicYear->id;
        }

        // Calculate due date
        $dueDate = Carbon::create($year, $month, $feeSettings->payment_deadline_day);

        // Get all students for the academic year
        $students = Student::where('academic_year_id', $academicYearId)->get();

        $createdCount = 0;
        foreach ($students as $student) {
            // Check if payment record already exists
            $existingPayment = self::where([
                'student_id' => $student->id,
                'academic_year_id' => $academicYearId,
                'month' => $month,
                'year' => $year,
            ])->first();

            if (!$existingPayment) {
                self::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYearId,
                    'month' => $month,
                    'year' => $year,
                    'fee_amount' => $feeSettings->monthly_fee_amount,
                    'fine_amount' => 0.00,
                    'total_amount' => $feeSettings->monthly_fee_amount,
                    'due_date' => $dueDate,
                    'is_paid' => false,
                    'is_overdue' => false,
                    'days_overdue' => 0,
                ]);
                $createdCount++;
            }
        }

        return $createdCount;
    }
}
