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
        Schema::create('fee_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('monthly_fee_amount', 10, 2)->default(0.00);
            $table->integer('payment_deadline_day')->default(10); // Day of month (1-31)
            $table->decimal('fine_amount_per_day', 8, 2)->default(0.00);
            $table->decimal('maximum_fine_amount', 10, 2)->nullable();
            $table->boolean('is_percentage_fine')->default(false);
            $table->decimal('fine_percentage', 5, 2)->nullable();
            $table->integer('grace_period_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_settings');
    }
};
