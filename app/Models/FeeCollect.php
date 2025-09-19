<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCollect extends Model
{
      use HasFactory;

    protected $fillable = ['academic_year_id','payment_method_id', 'semester_id', 'student_id','user_id','date', 'year', 'fee_heads', 'discount', 'fine_amount', 'overdue_days', 'fine_details', 'total_amount','total_payable','net_payable','months'];
    protected $casts = [
        'fee_heads' => 'array',
        'months' => 'array',
        'fine_details' => 'array',
        'fine_amount' => 'decimal:2',
    ];
     public function student()
    {
        return $this->belongsTo(Student::class);
    }
     public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function payment_method()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
