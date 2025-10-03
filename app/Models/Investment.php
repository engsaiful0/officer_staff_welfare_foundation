<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'principal_amount',
        'product_name',
        'start_date',
        'term_months',
        'expiry_date',
        'rate',
        'rate_period',
        'frequency',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'principal_amount' => 'decimal:2',
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

    public function rateHistories()
    {
        return $this->hasMany(RateHistory::class);
    }

    // Accessors & Mutators
    public function getRatePercentageAttribute()
    {
        return $this->rate * 100;
    }

    public function getCurrentBalanceAttribute()
    {
        $lastEntry = $this->ledgerEntries()
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return $lastEntry ? $lastEntry->balance_after : $this->principal_amount;
    }

    public function getTotalInterestAccruedAttribute()
    {
        return $this->ledgerEntries()
            ->where('type', 'accrual')
            ->sum('interest_amount');
    }

    public function getTotalPaymentsAttribute()
    {
        return $this->ledgerEntries()
            ->where('type', 'payment')
            ->sum('amount');
    }

    public function isMatured()
    {
        return $this->expiry_date <= Carbon::now()->toDateString();
    }

    public function isActive()
    {
        return $this->status === 'active' && !$this->isMatured();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeMatured($query)
    {
        return $query->where('expiry_date', '<=', Carbon::now()->toDateString());
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }
}
