<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'branch_name',
        'branch_address',
        'branch_code',
        'branch_phone',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
