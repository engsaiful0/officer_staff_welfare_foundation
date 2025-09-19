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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_name')->nullable();
            $table->string('teacher_unique_id')->unique()->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('nid')->nullable();
            $table->text('present_address');
            $table->text('permanent_address');
            $table->string('picture')->nullable();
            $table->string('nid_picture')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('basic_salary')->nullable();
            $table->string('house_rent')->nullable();
            $table->string('medical_allowance')->nullable();
            $table->string('other_allowance')->nullable();
            $table->string('gross_salary')->nullable();

            // SSC Details
            $table->string('ssc_or_equivalent_group')->nullable();
            $table->string('ssc_or_equivalent_gpa')->nullable();

            // HSC Details
            $table->string('hsc_or_equivalent_group')->nullable();
            $table->string('hsc_or_equivalent_gpa')->nullable();

            // Bachelor's Degree Details
            $table->string('bachelor_or_equivalent_group')->nullable();
            $table->string('bachelor_or_equivalent_gpa')->nullable();

            // Master's Degree Details
            $table->string('master_or_equivalent_group')->nullable();
            $table->string('master_or_equivalent_gpa')->nullable();

            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('religion_id')->constrained("religions")->onDelete('cascade');
            $table->foreignId('designation_id')->constrained("designations")->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
