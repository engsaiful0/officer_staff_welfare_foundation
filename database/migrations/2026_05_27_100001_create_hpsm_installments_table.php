<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hpsm_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hpsm_opening_account_id');
            $table->unsignedInteger('installment_no');
            $table->date('installment_date');
            $table->decimal('opening_principal', 15, 2);
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('pre_rent_amount', 15, 2)->default(0);
            $table->decimal('rent_amount', 15, 2);
            $table->decimal('total_installment', 15, 2);
            $table->decimal('closing_principal', 15, 2);
            $table->decimal('principal_paid', 15, 2)->default(0);
            $table->decimal('pre_rent_paid', 15, 2)->default(0);
            $table->decimal('rent_paid', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->date('paid_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('hpsm_opening_account_id', 'hpsm_inst_acct_fk')
                ->references('id')
                ->on('hpsm_opening_accounts')
                ->cascadeOnDelete();

            $table->unique(['hpsm_opening_account_id', 'installment_no'], 'hpsm_installments_acct_no_unique');
            $table->index(['hpsm_opening_account_id', 'payment_status'], 'hpsm_installments_acct_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hpsm_installments');
    }
};
