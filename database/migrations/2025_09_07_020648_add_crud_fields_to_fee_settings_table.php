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
        Schema::table('fee_settings', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->decimal('amount', 10, 2)->default(0)->after('name');
            $table->string('fine_type')->default('fixed')->after('fine_percentage');
            $table->unsignedBigInteger('user_id')->nullable()->after('notes');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_settings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['name', 'amount', 'fine_type', 'user_id']);
        });
    }
};
