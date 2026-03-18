<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quard extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'total_deposit_amount',
        'percentage_of_deposit',
        'quard_amount',
        'period_in_years',
        'installment_number',
        'installment_amount',
        'charge_percentage',
        'charge_amount',
        'start_date',
        'maturity_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'total_deposit_amount' => 'decimal:2',
        'quard_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'charge_amount' => 'decimal:2',
        'start_date' => 'date',
        'maturity_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
