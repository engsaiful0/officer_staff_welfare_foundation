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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->decimal('principal_amount', 15, 2);
            $table->string('product_name')->nullable();
            $table->date('start_date');
            $table->integer('term_months');
            $table->date('expiry_date');
            $table->decimal('rate', 8, 4); // Store as fraction (e.g., 0.15 for 15%)
            $table->enum('rate_period', ['annual', 'monthly']);
            $table->enum('frequency', ['monthly', 'quarterly', 'daily']);
            $table->enum('status', ['active', 'matured', 'closed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            
            // Indexes
            $table->index(['member_id', 'status']);
            $table->index('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
