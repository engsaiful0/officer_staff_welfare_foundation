<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hpsm_collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hpsm_opening_account_id');
            $table->unsignedBigInteger('hpsm_installment_id')->nullable();
            $table->date('collection_date');
            $table->decimal('principal_collected', 15, 2)->default(0);
            $table->decimal('pre_rent_collected', 15, 2)->default(0);
            $table->decimal('rent_collected', 15, 2)->default(0);
            $table->decimal('total_collected', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('transaction_no')->nullable();
            $table->unsignedBigInteger('collected_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('hpsm_opening_account_id', 'hpsm_coll_acct_fk')
                ->references('id')
                ->on('hpsm_opening_accounts')
                ->cascadeOnDelete();

            $table->foreign('hpsm_installment_id', 'hpsm_coll_inst_fk')
                ->references('id')
                ->on('hpsm_installments')
                ->nullOnDelete();

            $table->index('collection_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hpsm_collections');
    }
};
