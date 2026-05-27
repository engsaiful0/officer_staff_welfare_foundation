<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HpsmInstallment extends Model
{
    protected $table = 'hpsm_installments';

    protected $fillable = [
        'hpsm_opening_account_id',
        'installment_no',
        'installment_date',
        'opening_principal',
        'principal_amount',
        'pre_rent_amount',
        'rent_amount',
        'total_installment',
        'closing_principal',
        'principal_paid',
        'pre_rent_paid',
        'rent_paid',
        'paid_amount',
        'due_amount',
        'payment_status',
        'paid_date',
        'remarks',
    ];

    protected $casts = [
        'installment_date' => 'date',
        'paid_date' => 'date',
        'opening_principal' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'pre_rent_amount' => 'decimal:2',
        'rent_amount' => 'decimal:2',
        'total_installment' => 'decimal:2',
        'closing_principal' => 'decimal:2',
        'principal_paid' => 'decimal:2',
        'pre_rent_paid' => 'decimal:2',
        'rent_paid' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function openingAccount(): BelongsTo
    {
        return $this->belongsTo(HpsmOpeningAccount::class, 'hpsm_opening_account_id');
    }

    public function collections(): HasMany
    {
        return $this->hasMany(HpsmCollection::class, 'hpsm_installment_id');
    }

    public function preRentDue(): string
    {
        return bcsub((string) $this->pre_rent_amount, (string) $this->pre_rent_paid, 2);
    }

    public function rentDue(): string
    {
        return bcsub((string) $this->rent_amount, (string) $this->rent_paid, 2);
    }

    public function principalDue(): string
    {
        return bcsub((string) $this->principal_amount, (string) $this->principal_paid, 2);
    }

    public function totalDue(): string
    {
        $a = $this->preRentDue();
        $b = $this->rentDue();
        $c = $this->principalDue();

        return bcadd(bcadd($a, $b, 2), $c, 2);
    }

    public function refreshDueSnapshot(): void
    {
        $dueStr = $this->totalDue();
        if (bccomp($dueStr, '0', 2) === -1) {
            $dueStr = '0.00';
        }
        $this->due_amount = $dueStr;
        $paid = (string) $this->paid_amount;

        if (bccomp($dueStr, '0', 2) !== 1) {
            $this->payment_status = 'paid';
            $this->paid_date = $this->paid_date ?? now()->toDateString();
        } elseif (bccomp($paid, '0', 2) === 1) {
            $this->payment_status = 'partial';
            $this->paid_date = null;
        } else {
            $this->payment_status = 'pending';
            $this->paid_date = null;
        }
        $this->save();
    }
}
