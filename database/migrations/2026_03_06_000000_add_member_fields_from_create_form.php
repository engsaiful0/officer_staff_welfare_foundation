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
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('spouse_name')->nullable()->after('mother_name');
            $table->string('employees_id')->nullable()->after('religion_id');
            $table->string('nominee_father_name')->nullable()->after('nominee_name');
            $table->string('nominee_mother_name')->nullable()->after('nominee_father_name');
            $table->string('nominee_spouse_name')->nullable()->after('nominee_mother_name');
            $table->text('nominee_present_address')->nullable()->after('nominee_phone');
            $table->text('nominee_permanent_address')->nullable()->after('nominee_present_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'mother_name',
                'spouse_name',
                'employees_id',
                'nominee_father_name',
                'nominee_mother_name',
                'nominee_spouse_name',
                'nominee_present_address',
                'nominee_permanent_address',
            ]);
        });
    }
};
