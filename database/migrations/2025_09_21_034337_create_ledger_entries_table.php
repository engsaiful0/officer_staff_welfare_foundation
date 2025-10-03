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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investment_id');
            $table->date('entry_date');
            $table->enum('type', ['accrual', 'payment', 'credit', 'adjustment', 'principal']);
            $table->decimal('amount', 15, 2);
            $table->decimal('interest_amount', 15, 2)->nullable();
            $table->decimal('principal_amount', 15, 2)->nullable();
            $table->decimal('balance_after', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('investment_id')->references('id')->on('investments')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index(['investment_id', 'entry_date']);
            $table->index('type');
            $table->index('entry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
