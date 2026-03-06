<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositInstallmentAmount extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'installment_amount',
        'date',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'installment_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
