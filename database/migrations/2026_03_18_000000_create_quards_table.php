<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->decimal('total_deposit_amount', 15, 2)->default(0);
            $table->decimal('percentage_of_deposit', 8, 2)->default(0);
            $table->decimal('quard_amount', 15, 2)->default(0);
            $table->unsignedInteger('period_in_years')->default(1);
            $table->unsignedInteger('installment_number')->default(1);
            $table->decimal('installment_amount', 15, 2)->default(0);
            $table->decimal('charge_percentage', 8, 2)->default(0);
            $table->decimal('charge_amount', 15, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('maturity_date')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quards');
    }
};

