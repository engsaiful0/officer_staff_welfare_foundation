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
            $table->decimal('discount_amount', 15, 2)->default(0)->after('fine_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investment_installments', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
        });
    }
};
