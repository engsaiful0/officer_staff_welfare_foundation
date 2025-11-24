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
        Schema::create('investment_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('investment_id')->unique();
            $table->string('account_number')->unique()->nullable();
            $table->date('account_opening_date');
            $table->date('account_closing_date')->nullable();
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->decimal('total_principal_paid', 15, 2)->default(0);
            $table->decimal('total_interest_received', 15, 2)->default(0);
            $table->decimal('total_rent_received', 15, 2)->default(0);
            $table->decimal('total_payments_made', 15, 2)->default(0);
            $table->decimal('total_installments_paid', 15, 2)->default(0);
            $table->integer('installments_paid_count')->default(0);
            $table->integer('installments_pending_count')->default(0);
            $table->integer('installments_overdue_count')->default(0);
            $table->enum('account_status', ['active', 'closed', 'matured', 'suspended'])->default('active');
            $table->text('account_notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('investment_id')->references('id')->on('investments')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('account_number');
            $table->index('account_status');
            $table->index('account_opening_date');
            $table->index('account_closing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_accounts');
    }
};
