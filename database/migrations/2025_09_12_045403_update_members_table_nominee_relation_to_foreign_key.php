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
        Schema::table('members', function (Blueprint $table) {
            // Drop the old nominee_relation column
            $table->dropColumn('nominee_relation');
            
            // Add the new foreign key column
            $table->unsignedBigInteger('nominee_relation_id')->nullable()->after('nominee_name');
            
            // Add foreign key constraint
            $table->foreign('nominee_relation_id')->references('id')->on('relations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['nominee_relation_id']);
            
            // Drop the foreign key column
            $table->dropColumn('nominee_relation_id');
            
            // Add back the old string column
            $table->string('nominee_relation')->nullable()->after('nominee_name');
        });
    }
};