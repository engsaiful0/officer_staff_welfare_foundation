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
        Schema::table('designations', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('designation_type');
        });
        
        Schema::table('designations', function (Blueprint $table) {
            // Recreate the column with proper length
            $table->enum('designation_type', ['Member', 'Employee', 'Management'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('designations', function (Blueprint $table) {
            $table->dropColumn('designation_type');
        });
        
        Schema::table('designations', function (Blueprint $table) {
            $table->enum('designation_type', ['Member', 'Employee', 'Management'])->nullable();
        });
    }
};
