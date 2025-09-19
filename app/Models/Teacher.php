<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_name',
        'teacher_unique_id',
        'father_name',
        'mother_name',
        'mobile',
        'gender',
        'email',
        'nid',
        'present_address',
        'permanent_address',
        'designation_id',
        'picture',
        'nid_picture',
        'joining_date',
        'basic_salary',
        'gross_salary',
        'house_rent',
        'medical_allowance',
        'other_allowance',
        'ssc_or_equivalent_group',
        'ssc_or_equivalent_gpa',
        'hsc_or_equivalent_group',
        'hsc_or_equivalent_gpa',
        'bachelor_or_equivalent_group',
        'bachelor_or_equivalent_gpa',
        'master_or_equivalent_group',
        'master_or_equivalent_gpa',
        'religion_id',
        'designation_id',
    ];
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
}
