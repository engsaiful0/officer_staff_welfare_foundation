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
        Schema::table('fee_collects', function (Blueprint $table) {
            $table->decimal('fine_amount', 10, 2)->default(0)->after('discount');
            $table->integer('overdue_days')->default(0)->after('fine_amount');
            $table->json('fine_details')->nullable()->after('overdue_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_collects', function (Blueprint $table) {
            $table->dropColumn(['fine_amount', 'overdue_days', 'fine_details']);
        });
    }
};
