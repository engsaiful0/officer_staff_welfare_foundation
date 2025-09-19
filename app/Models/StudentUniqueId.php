<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentUniqueId extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_unique_id',
        'serial',
        'student_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
