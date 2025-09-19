<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentMonthlyFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'month_id',
        'fee_collect_id',
        'amount',
        'payment_date',
        'is_paid',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'is_paid' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function feeCollect()
    {
        return $this->belongsTo(FeeCollect::class);
    }
}
