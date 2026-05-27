<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberUniqueId extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_unique_id',
        'serial',
        'member_id'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
