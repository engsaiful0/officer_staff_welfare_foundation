<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'monthly_fee_amount',
        'payment_deadline_day',
        'fine_type',
        'fine_amount_per_day',
        'maximum_fine_amount',
        'is_percentage_fine',
        'fine_percentage',
        'grace_period_days',
        'is_active',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'monthly_fee_amount' => 'decimal:2',
        'fine_amount_per_day' => 'decimal:2',
        'maximum_fine_amount' => 'decimal:2',
        'fine_percentage' => 'decimal:2',
        'is_percentage_fine' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the active fee settings
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Get the user that created this fee settings
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate fine amount for given overdue days
     */
    public function calculateFineAmount($daysOverdue, $feeAmount = null)
    {
        if ($daysOverdue <= $this->grace_period_days) {
            return 0;
        }

        $effectiveDays = $daysOverdue - $this->grace_period_days;

        if ($this->is_percentage_fine) {
            $feeAmount = $feeAmount ?? $this->monthly_fee_amount ?? $this->amount;
            $fineAmount = ($feeAmount * $this->fine_percentage / 100) * $effectiveDays;
        } else {
            $fineAmount = $this->fine_amount_per_day * $effectiveDays;
        }

        // Apply maximum fine limit if set
        if ($this->maximum_fine_amount && $fineAmount > $this->maximum_fine_amount) {
            $fineAmount = $this->maximum_fine_amount;
        }

        return round($fineAmount, 2);
    }
}
