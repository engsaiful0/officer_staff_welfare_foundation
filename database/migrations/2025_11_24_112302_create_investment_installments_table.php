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
        Schema::create('investment_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investment_id');
            $table->integer('installment_number');
            $table->date('schedule_date');
            $table->decimal('beginning_balance', 15, 2);
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('rent', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('ending_balance', 15, 2);
            $table->decimal('cumulative_rent', 15, 2);
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('investment_id')->references('id')->on('investments')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('paid_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_installments');
    }
};
