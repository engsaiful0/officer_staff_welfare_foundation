<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hpsm_opening_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->string('account_no')->unique();
            $table->decimal('balance_principal', 15, 2);
            $table->decimal('balance_pre_rent', 15, 2);
            $table->decimal('current_rent', 15, 2);
            $table->decimal('annual_profit_rate', 8, 2);
            $table->unsignedInteger('remaining_duration_months');
            $table->decimal('monthly_principal', 15, 2);
            $table->decimal('current_outstanding_principal', 15, 2);
            $table->decimal('total_opening_balance', 15, 2);
            $table->date('opening_date');
            $table->enum('status', ['active', 'completed', 'closed'])->default('active');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('member_id')->references('id')->on('members')->restrictOnDelete();
            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hpsm_opening_accounts');
    }
};
