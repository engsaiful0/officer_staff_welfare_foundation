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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->decimal('deposit_amount', 15, 2);
            $table->string('product_name')->nullable();
            $table->date('start_date');
            $table->date('maturity_date')->nullable();
            $table->decimal('rate', 8, 4)->nullable(); // e.g., 0.08 for 8%
            $table->enum('deposit_type', ['savings', 'fixed', 'recurring']);
            $table->enum('status', ['active', 'matured', 'closed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            
            // Indexes
            $table->index(['member_id', 'status']);
            $table->index('maturity_date');
            $table->index('deposit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
