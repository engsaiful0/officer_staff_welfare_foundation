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
        Schema::create('student_fee_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            
            // Semester fees tracking (8 semesters)
            $table->json('semester_fees_paid')->nullable(); // Array of semester IDs that are paid
            $table->decimal('total_semester_fees', 10, 2)->default(0);
            $table->decimal('paid_semester_fees', 10, 2)->default(0);
            
            // Monthly fees tracking (48 months over 4 years)
            $table->json('monthly_fees_paid')->nullable(); // Array of month IDs that are paid
            $table->decimal('total_monthly_fees', 10, 2)->default(0);
            $table->decimal('paid_monthly_fees', 10, 2)->default(0);
            
            // Overall totals
            $table->decimal('total_fees', 10, 2)->default(0);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->decimal('total_due', 10, 2)->default(0);
            
            // Completion status
            $table->boolean('all_semester_fees_paid')->default(false);
            $table->boolean('all_monthly_fees_paid')->default(false);
            $table->boolean('all_fees_paid')->default(false);
            
            // Progress tracking
            $table->integer('semesters_completed')->default(0);
            $table->integer('months_completed')->default(0);
            $table->integer('total_semesters')->default(8);
            $table->integer('total_months')->default(48);
            
            $table->timestamps();
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['student_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_fee_summaries');
    }
};
