<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name_in_banglai',
        'full_name_in_english_block_letter',
        'father_name_in_banglai',
        'student_unique_id',
        'father_name_in_english_block_letter',
        'mother_name_in_banglai',
        'mother_name_in_english_block_letter',
        'guardian_name_absence_of_father',
        'personal_number',
        'email',
        'guardian_phone',
        'present_address',
        'permanent_address',
        'date_of_birth',
        'ssc_or_equivalent_institute_name',
        'ssc_or_equivalent_institute_address',
        'ssc_or_equivalent_number_potro',
        'ssc_or_equivalent_roll_number',
        'ssc_or_equivalent_registration_number',
        'ssc_or_equivalent_session_id',
        'ssc_or_equivalent_passing_year_id',
        'ssc_or_equivalent_gpa',
        'last_institute_testimonial',
        'picture',
        'applicant_declaration',
        'nationality_id',
        'religion_id',
        'board_id',
        'technology_id',
        'shift_id',
        'academic_year_id',
        'semester_id',
        'gender',
    ];

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function technology()
    {
        return $this->belongsTo(Technology::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function semesterFees()
    {
        return $this->hasMany(StudentSemesterFee::class);
    }

    public function monthlyFees()
    {
        return $this->hasMany(StudentMonthlyFee::class);
    }

    public function feeSummary()
    {
        return $this->hasOne(StudentFeeSummary::class);
    }

    public function feeCollections()
    {
        return $this->hasMany(FeeCollect::class);
    }
}
