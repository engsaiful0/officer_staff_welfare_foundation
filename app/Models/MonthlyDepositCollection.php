<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;

class MonthlyDepositCollection extends Model
{
    use HasFactory;

    protected $fillable = [
        'deposit_id',
        'member_id',
        'collection_number',
        'collection_date',
        'amount',
        'month',
        'description',
        'created_by'
    ];

    protected $casts = [
        'collection_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    public function getFormattedDateAttribute()
    {
        return $this->collection_date->format('M d, Y');
    }

    // Scopes
    public function scopeByDeposit($query, $depositId)
    {
        return $query->where('deposit_id', $depositId);
    }

    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByDateRange($query, $dateFrom, $dateTo)
    {
        return $query->whereBetween('collection_date', [$dateFrom, $dateTo]);
    }

    public function scopeByMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    /**
     * Generate collection number
     */
    public static function generateCollectionNumber()
    {
        $year = Carbon::now()->year;
        $lastCollection = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $serial = $lastCollection ? (int) substr($lastCollection->collection_number, -6) + 1 : 1;
        
        return 'MDC' . $year . str_pad($serial, 6, '0', STR_PAD_LEFT);
    }
}
