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
        'deposit_amount',
        'product_name',
        'start_date',
        'maturity_date',
        'rate',
        'deposit_type',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'maturity_date' => 'date',
        'deposit_amount' => 'decimal:2',
        'rate' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'entity');
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

    public function scopeByType($query, $type)
    {
        return $query->where('deposit_type', $type);
    }

    public function scopeWithInterest($query)
    {
        return $query->whereNotNull('rate')->where('rate', '>', 0);
    }
}
