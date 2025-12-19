<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DepositAccountNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_number',
        'serial',
        'user_id',
        'deposit_id',
        'year'
    ];

    protected $casts = [
        'serial' => 'integer',
        'year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }

    // Scopes
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBySerial($query, $serial)
    {
        return $query->where('serial', $serial);
    }

    // Static methods
    public static function getNextSerial($year = null)
    {
        if (!$year) {
            $year = Carbon::now()->year;
        }

        $lastRecord = self::where('year', $year)
            ->orderBy('serial', 'desc')
            ->first();

        return $lastRecord ? $lastRecord->serial + 1 : 1;
    }

    public static function generateAccountNumber($userId, $depositId = null, $year = null)
    {
        if (!$year) {
            $year = Carbon::now()->year;
        }

        $serial = self::getNextSerial($year);
        $accountNumber = 'DEP-ACC-' . $year . '-' . str_pad($serial, 6, '0', STR_PAD_LEFT);

        $accountNumberRecord = self::create([
            'account_number' => $accountNumber,
            'serial' => $serial,
            'user_id' => $userId,
            'deposit_id' => $depositId,
            'year' => $year,
        ]);

        return $accountNumberRecord;
    }
}
