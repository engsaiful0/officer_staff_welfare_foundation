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
        Schema::create('monthly_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->integer('month')->comment('Month number (1-12)');
            $table->integer('year');
            $table->decimal('fee_amount', 10, 2);
            $table->decimal('fine_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_overdue')->default(false);
            $table->integer('days_overdue')->default(0);
            $table->foreignId('fee_collect_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent duplicate entries for same student/month/year
            $table->unique(['student_id', 'academic_year_id', 'month', 'year'], 'monthly_fee_payments_unique');
            
            // Indexes for better performance
            $table->index(['is_paid', 'due_date']);
            $table->index(['is_overdue', 'due_date']);
            $table->index(['month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_fee_payments');
    }
};
