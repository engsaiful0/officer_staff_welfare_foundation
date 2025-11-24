<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InvestmentInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_id',
        'installment_number',
        'schedule_date',
        'beginning_balance',
        'principal_amount',
        'rent',
        'total_amount',
        'ending_balance',
        'cumulative_rent',
        'status',
        'paid_date'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'paid_date' => 'date',
        'beginning_balance' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'rent' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'ending_balance' => 'decimal:2',
        'cumulative_rent' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->where('schedule_date', '<', Carbon::now()->toDateString());
    }

    public function scopeByInvestment($query, $investmentId)
    {
        return $query->where('investment_id', $investmentId);
    }

    public function isOverdue()
    {
        return $this->status === 'pending' && $this->schedule_date < Carbon::now()->toDateString();
    }
}
