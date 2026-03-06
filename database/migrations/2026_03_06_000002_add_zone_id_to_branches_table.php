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
        Schema::table('branches', function (Blueprint $table) {
            $table->unsignedBigInteger('zone_id')->nullable()->after('id');
            $table->string('branch_code')->nullable()->after('branch_address');
            $table->string('branch_phone', 50)->nullable()->after('branch_code');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropColumn(['zone_id', 'branch_code', 'branch_phone']);
        });
    }
};
