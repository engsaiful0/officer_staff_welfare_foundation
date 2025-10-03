<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_id',
        'old_rate',
        'new_rate',
        'effective_date',
        'reason',
        'created_by'
    ];

    protected $casts = [
        'effective_date' => 'date',
        'old_rate' => 'decimal:4',
        'new_rate' => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getOldRatePercentageAttribute()
    {
        return $this->old_rate * 100;
    }

    public function getNewRatePercentageAttribute()
    {
        return $this->new_rate * 100;
    }

    public function getRateChangeAttribute()
    {
        return $this->new_rate - $this->old_rate;
    }

    public function getRateChangePercentageAttribute()
    {
        return $this->rate_change * 100;
    }
}
