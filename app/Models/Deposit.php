<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'account_number',
        'deposit_account_number',
        'deposit_amount',
        'monthly_deposit_amount',
        'deposit_day_of_month',
        'last_deposit_date',
        'product_name',
        'start_date',
        'maturity_date',
        'rate',
        'deposit_type_id',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'maturity_date' => 'date',
        'last_deposit_date' => 'date',
        'deposit_amount' => 'decimal:2',
        'monthly_deposit_amount' => 'decimal:2',
        'rate' => 'decimal:4',
        'deposit_day_of_month' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function depositType()
    {
        return $this->belongsTo(DepositType::class);
    }

    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'entity');
    }

    public function accountNumberRecord()
    {
        return $this->hasOne(DepositAccountNumber::class);
    }

    // Accessors & Mutators
    public function getRatePercentageAttribute()
    {
        return $this->rate ? $this->rate * 100 : 0;
    }

    public function getCurrentBalanceAttribute()
    {
        $lastEntry = $this->ledgerEntries()
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return $lastEntry ? $lastEntry->balance_after : $this->deposit_amount;
    }

    public function getTotalInterestAccruedAttribute()
    {
        return $this->ledgerEntries()
            ->whereIn('type', ['accrual', 'interest'])
            ->sum('amount');
    }

    public function getTotalWithdrawalsAttribute()
    {
        return $this->ledgerEntries()
            ->where('type', 'withdrawal')
            ->sum('amount');
    }

    public function getTotalDepositsAttribute()
    {
        return $this->ledgerEntries()
            ->where('type', 'deposit')
            ->sum('amount');
    }

    public function isMatured()
    {
        return $this->maturity_date && $this->maturity_date <= Carbon::now()->toDateString();
    }

    public function isActive()
    {
        return $this->status === 'active' && !$this->isMatured();
    }

    public function hasInterestRate()
    {
        return $this->rate && $this->rate > 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeMatured($query)
    {
        return $query->where('maturity_date', '<=', Carbon::now()->toDateString());
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('deposit_type_id', $typeId);
    }

    public function scopeWithInterest($query)
    {
        return $query->whereNotNull('rate')->where('rate', '>', 0);
    }

    public function scopeWithMonthlyDeposits($query)
    {
        return $query->whereNotNull('monthly_deposit_amount')->where('monthly_deposit_amount', '>', 0);
    }

    /**
     * Generate account number for deposit
     */
    public function generateAccountNumber()
    {
        if (!$this->account_number) {
            $userId = $this->created_by ?? auth()->id();
            
            // Generate account number using DepositAccountNumber model
            $accountNumberRecord = DepositAccountNumber::generateAccountNumber(
                $userId,
                null, // Will be updated after deposit is saved
                Carbon::now()->year
            );

            $this->account_number = $accountNumberRecord->account_number;
            $this->save();
            
            // Update the account_number_record with the deposit_id
            if ($this->id) {
                $accountNumberRecord->update([
                    'deposit_id' => $this->id
                ]);
            }
        }

        return $this->account_number;
    }

    /**
     * Check if monthly deposit is due
     */
    public function isMonthlyDepositDue($date = null)
    {
        if (!$this->monthly_deposit_amount || $this->monthly_deposit_amount <= 0) {
            return false;
        }

        if ($this->status !== 'active') {
            return false;
        }

        $checkDate = $date ? Carbon::parse($date) : Carbon::now();
        $dayOfMonth = $this->deposit_day_of_month ?? 1;

        // Check if we've already processed this month
        if ($this->last_deposit_date) {
            $lastDeposit = Carbon::parse($this->last_deposit_date);
            if ($lastDeposit->year == $checkDate->year && $lastDeposit->month == $checkDate->month) {
                return false; // Already processed this month
            }
        }

        // Check if the deposit day has passed this month
        if ($checkDate->day >= $dayOfMonth) {
            return true;
        }

        return false;
    }
}
