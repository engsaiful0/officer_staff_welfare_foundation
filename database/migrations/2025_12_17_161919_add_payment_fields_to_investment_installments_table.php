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
        Schema::table('investment_installments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('paid_by');
            $table->string('transaction_reference')->nullable()->after('payment_method_id');
            $table->string('receipt_number')->nullable()->unique()->after('transaction_reference');
            $table->string('bank_name')->nullable()->after('receipt_number');
            $table->string('check_number')->nullable()->after('bank_name');
            
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_installments', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['payment_method_id', 'transaction_reference', 'receipt_number', 'bank_name', 'check_number']);
        });
    }
};
