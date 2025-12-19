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
        Schema::table('deposits', function (Blueprint $table) {
            $table->string('account_number')->unique()->nullable()->after('id');
            $table->decimal('monthly_deposit_amount', 15, 2)->nullable()->after('deposit_amount');
            $table->integer('deposit_day_of_month')->default(1)->after('monthly_deposit_amount'); // Day of month for monthly deposits
            $table->date('last_deposit_date')->nullable()->after('deposit_day_of_month'); // Track last monthly deposit
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'monthly_deposit_amount', 'deposit_day_of_month', 'last_deposit_date']);
        });
    }
};
