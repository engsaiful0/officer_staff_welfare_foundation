<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HpsmOpeningAccount extends Model
{
    use SoftDeletes;

    protected $table = 'hpsm_opening_accounts';

    protected $fillable = [
        'member_id',
        'account_no',
        'balance_principal',
        'balance_pre_rent',
        'current_rent',
        'annual_profit_rate',
        'remaining_duration_months',
        'monthly_principal',
        'current_outstanding_principal',
        'total_opening_balance',
        'opening_date',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'balance_principal' => 'decimal:2',
        'balance_pre_rent' => 'decimal:2',
        'current_rent' => 'decimal:2',
        'annual_profit_rate' => 'decimal:2',
        'monthly_principal' => 'decimal:2',
        'current_outstanding_principal' => 'decimal:2',
        'total_opening_balance' => 'decimal:2',
        'opening_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(HpsmInstallment::class, 'hpsm_opening_account_id')->orderBy('installment_no');
    }

    public function collections(): HasMany
    {
        return $this->hasMany(HpsmCollection::class, 'hpsm_opening_account_id')->orderByDesc('collection_date')->orderByDesc('id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
