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
        'fine_amount',
        'discount_amount',
        'total_amount',
        'ending_balance',
        'cumulative_rent',
        'status',
        'paid_date',
        'notes',
        'created_by',
        'paid_by',
        'updated_by'
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'paid_date' => 'date',
        'beginning_balance' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'rent' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
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

    /**
     * Calculate fine amount for late payment
     * Formula: ((capital_amount_of_month * days_late) / 365) * interest_rate
     * 
     * @param Carbon|null $paidDate The date when payment is made (defaults to today)
     * @return float
     */
    public function calculateFine($paidDate = null)
    {
        if ($paidDate === null) {
            $paidDate = Carbon::now();
        } elseif (is_string($paidDate)) {
            $paidDate = Carbon::parse($paidDate);
        }

        // Only calculate fine if payment is late
        if ($paidDate->lte($this->schedule_date)) {
            return 0;
        }

        // Calculate days late
        $daysLate = $paidDate->diffInDays($this->schedule_date);

        // Get investment rate
        $investment = $this->investment;
        if (!$investment) {
            return 0;
        }

        // Capital amount of month = principal_amount
        $capitalAmount = $this->principal_amount;
        $interestRate = $investment->rate; // Already in decimal form (e.g., 0.15 for 15%)

        // Calculate fine: ((capital_amount * days_late) / 365) * interest_rate
        $fine = (($capitalAmount * $daysLate) / 365) * $interestRate;

        return round($fine, 2);
    }

    /**
     * Get days late for this installment
     * 
     * @param Carbon|null $paidDate The date when payment is made (defaults to today)
     * @return int
     */
    public function getDaysLate($paidDate = null)
    {
        if ($paidDate === null) {
            $paidDate = Carbon::now();
        } elseif (is_string($paidDate)) {
            $paidDate = Carbon::parse($paidDate);
        }

        if ($paidDate->lte($this->schedule_date)) {
            return 0;
        }

        return $paidDate->diffInDays($this->schedule_date);
    }

    /**
     * Update fine amount based on paid date
     * 
     * @param Carbon|string|null $paidDate
     * @return self
     */
    public function updateFine($paidDate = null)
    {
        $fine = $this->calculateFine($paidDate);
        $this->fine_amount = (float)$fine;
        
        // Update total amount to include fine
        $this->total_amount = (float)$this->principal_amount + (float)$this->rent + (float)$fine;
        
        return $this;
    }
}
