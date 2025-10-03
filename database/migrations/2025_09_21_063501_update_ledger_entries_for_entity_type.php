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
        Schema::table('ledger_entries', function (Blueprint $table) {
            // Add entity_type and entity_id columns for polymorphic relationship
            $table->string('entity_type')->default('investment')->after('id');
            $table->unsignedBigInteger('entity_id')->after('entity_type');
            
            // Update the type enum to include deposit-specific types
            $table->dropColumn('type');
        });
        
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->enum('type', ['deposit', 'withdrawal', 'accrual', 'interest', 'adjustment', 'principal', 'payment', 'credit'])->after('entity_id');
            
            // Add indexes for the new columns
            $table->index(['entity_type', 'entity_id']);
            $table->index('entity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropIndex(['entity_type', 'entity_id']);
            $table->dropIndex(['entity_type']);
            $table->dropColumn(['entity_type', 'entity_id']);
        });
        
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->enum('type', ['accrual', 'payment', 'credit', 'adjustment', 'principal'])->after('investment_id');
        });
    }
};
