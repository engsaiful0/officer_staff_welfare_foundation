<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDeduction extends Model
{
    protected $fillable = [
        'member_id',
        'month',
        'year',
        'monthly_deposit_amount',
        'monthly_investment_amount',
        'monthly_qard_amount',
        'profit_on_deposit_amount',
        'compensation_on_investment_amount',
        'total_amount',
        'deduction_date',
        'remarks',
        'user_id',
    ];

    protected $casts = [
        'monthly_deposit_amount' => 'decimal:2',
        'monthly_investment_amount' => 'decimal:2',
        'monthly_qard_amount' => 'decimal:2',
        'profit_on_deposit_amount' => 'decimal:2',
        'compensation_on_investment_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'deduction_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
