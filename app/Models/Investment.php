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
        'investment_type_id',
        'principal_amount',
        'selling_price',
        'profit_amount',
        'emi_amount',
        'remaining_principal',
        'ownership_ratio',
        'product_name',
        'calculation_method',
        'start_date',
        'account_opening_date',
        'gestation_date',
        'term_months',
        'expiry_date',
        'rate',
        'rate_period',
        'frequency',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'account_opening_date' => 'date',
        'gestation_date' => 'date',
        'expiry_date' => 'date',
        'principal_amount' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit_amount' => 'decimal:2',
        'emi_amount' => 'decimal:2',
        'remaining_principal' => 'decimal:2',
        'ownership_ratio' => 'decimal:4',
        'rate' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function investmentType()
    {
        return $this->belongsTo(InvestmentType::class, 'investment_type_id');
    }

    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'entity');
    }

    public function rateHistories()
    {
        return $this->hasMany(RateHistory::class);
    }

    public function installments()
    {
        return $this->hasMany(InvestmentInstallment::class)->orderBy('installment_number');
    }

    public function account()
    {
        return $this->hasOne(InvestmentAccount::class);
    }

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

    public function isHpsm(): bool
    {
        $code = strtolower((string) ($this->investmentType?->code ?? ''));
        if ($code === 'hpsm') {
            return true;
        }

        return str_contains(strtolower((string) $this->product_name), 'hpsm');
    }

    public function isBaiMuajjal(): bool
    {
        $code = strtolower((string) ($this->investmentType?->code ?? ''));

        return $code === 'bai_muajjal' || str_contains(strtolower((string) $this->product_name), 'muajjal');
    }

    public function isMatured()
    {
        return $this->expiry_date <= Carbon::now()->toDateString();
    }

    public function isActive()
    {
        return $this->status === 'active' && ! $this->isMatured();
    }

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
