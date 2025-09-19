<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeHead extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'fee_type', 'month_id', 'semester_id','amount', 'is_discountable','date'];
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function month()
    {
        return $this->belongsTo(Month::class);
    }
}
