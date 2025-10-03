<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'entry_date',
        'type',
        'amount',
        'interest_amount',
        'principal_amount',
        'balance_after',
        'description',
        'created_by'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function entity()
    {
        return $this->morphTo();
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class, 'entity_id')->where('entity_type', 'investment');
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'entity_id')->where('entity_type', 'deposit');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('entry_date', [$startDate, $endDate]);
    }

    public function scopeAccruals($query)
    {
        return $query->where('type', 'accrual');
    }

    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    public function scopeInterest($query)
    {
        return $query->where('type', 'interest');
    }

    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)->where('entity_id', $entityId);
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance_after, 2);
    }
}
