<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'investment_type_name',
        'code',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function investments()
    {
        return $this->hasMany(Investment::class, 'investment_type_id');
    }

    public function isHpsm(): bool
    {
        return strtolower((string) $this->code) === 'hpsm'
            || str_contains(strtolower((string) $this->investment_type_name), 'hpsm');
    }

    public function isBaiMuajjal(): bool
    {
        return strtolower((string) $this->code) === 'bai_muajjal'
            || str_contains(strtolower((string) $this->investment_type_name), 'muajjal');
    }
}

