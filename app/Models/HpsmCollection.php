<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HpsmCollection extends Model
{
    protected $table = 'hpsm_collections';

    protected $fillable = [
        'hpsm_opening_account_id',
        'hpsm_installment_id',
        'collection_date',
        'principal_collected',
        'pre_rent_collected',
        'rent_collected',
        'total_collected',
        'payment_method',
        'transaction_no',
        'collected_by',
        'remarks',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'principal_collected' => 'decimal:2',
        'pre_rent_collected' => 'decimal:2',
        'rent_collected' => 'decimal:2',
        'total_collected' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function openingAccount(): BelongsTo
    {
        return $this->belongsTo(HpsmOpeningAccount::class, 'hpsm_opening_account_id');
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(HpsmInstallment::class, 'hpsm_installment_id');
    }
}
