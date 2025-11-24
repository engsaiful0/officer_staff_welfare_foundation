<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign key constraint if it exists
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'ledger_entries' 
            AND COLUMN_NAME = 'investment_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        foreach ($foreignKeys as $foreignKey) {
            DB::statement("ALTER TABLE ledger_entries DROP FOREIGN KEY {$foreignKey->CONSTRAINT_NAME}");
        }
        
        // Drop the column if it exists
        Schema::table('ledger_entries', function (Blueprint $table) {
            if (Schema::hasColumn('ledger_entries', 'investment_id')) {
                $table->dropColumn('investment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('ledger_entries', 'investment_id')) {
                $table->unsignedBigInteger('investment_id')->after('entity_id')->nullable();
                $table->foreign('investment_id')->references('id')->on('investments')->onDelete('cascade');
            }
        });
    }
};
