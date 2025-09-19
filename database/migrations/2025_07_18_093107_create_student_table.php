<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('full_name_in_banglai')->nullable();
            $table->string('full_name_in_english_block_letter')->nullable();
            $table->string('father_name_in_banglai')->nullable();
            $table->string('father_name_in_english_block_letter')->nullable();
            $table->string('mother_name_in_banglai')->nullable();
            $table->string('mother_name_in_english_block_letter')->nullable();
            $table->string('guardian_name_absence_of_father')->nullable();
            $table->string('personal_number')->nullable();
            $table->string('email')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('present_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('gender')->nullable();
            $table->string('student_unique_id')->nullable();
            $table->date('date_of_birth')->nullable(); // Better than string

            // SSC Details
            $table->string('ssc_or_equivalent_institute_name')->nullable();
            $table->string('ssc_or_equivalent_institute_address')->nullable();
            $table->string('ssc_or_equivalent_number_potro')->nullable();
            $table->string('ssc_or_equivalent_roll_number')->nullable();
            $table->string('ssc_or_equivalent_registration_number')->nullable();
            $table->string('ssc_or_equivalent_gpa')->nullable();

            // Attachments
            $table->string('last_institute_testimonial')->nullable();
            $table->string('picture')->nullable();
            $table->string('applicant_declaration')->nullable();

            // Foreign keys
            $table->unsignedBigInteger('nationality_id')->nullable();
            $table->unsignedBigInteger('religion_id')->nullable();
            $table->unsignedBigInteger('board_id')->nullable();
            $table->unsignedBigInteger('technology_id')->nullable();
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedBigInteger('semester_id')->nullable();
            $table->unsignedBigInteger('ssc_or_equivalent_session_id')->nullable();
            $table->unsignedBigInteger('ssc_or_equivalent_passing_year_id')->nullable();

            $table->timestamps();

            // Foreign Key Constraints (optional: you can also use cascade/delete rules)
            $table->foreign('ssc_or_equivalent_session_id')->references('id')->on('ssc_passing_sessions');
            $table->foreign('ssc_or_equivalent_passing_year_id')->references('id')->on('ssc_passing_years');
            $table->foreign('nationality_id')->references('id')->on('nationalities');
            $table->foreign('religion_id')->references('id')->on('religions');
            $table->foreign('board_id')->references('id')->on('boards');
            $table->foreign('technology_id')->references('id')->on('technologies');
            $table->foreign('shift_id')->references('id')->on('shifts');
            $table->foreign('academic_year_id')->references('id')->on('academic_years');
            $table->foreign('semester_id')->references('id')->on('semesters');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
