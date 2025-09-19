<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherUniqueId extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_unique_id',
        'serial',
        'teacher_id'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
