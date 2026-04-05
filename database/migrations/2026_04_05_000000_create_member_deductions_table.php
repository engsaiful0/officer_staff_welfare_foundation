<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('monthly_deposit_amount', 15, 2)->default(0);
            $table->decimal('monthly_investment_amount', 15, 2)->default(0);
            $table->decimal('monthly_qard_amount', 15, 2)->default(0);
            $table->decimal('profit_on_deposit_amount', 15, 2)->default(0);
            $table->decimal('compensation_on_investment_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->date('deduction_date');
            $table->text('remarks')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['member_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_deductions');
    }
};
