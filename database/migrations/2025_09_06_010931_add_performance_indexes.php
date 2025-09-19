<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            // Add indexes for frequently searched columns
            $table->index('academic_year_id');
            $table->index('semester_id');
            $table->index('technology_id');
            $table->index('shift_id');
            $table->index('student_unique_id');
            $table->index('personal_number');
            $table->index('full_name_in_english_block_letter');
            $table->index('created_at');
        });

        Schema::table('expenses', function (Blueprint $table) {
            // Add indexes for frequently searched columns
            $table->index('expense_head_id');
            $table->index('expense_date');
            $table->index('amount');
            $table->index('created_at');
        });

        Schema::table('student_unique_ids', function (Blueprint $table) {
            // Add indexes for frequently searched columns
            $table->index('student_id');
            $table->index('serial');
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['academic_year_id']);
            $table->dropIndex(['semester_id']);
            $table->dropIndex(['technology_id']);
            $table->dropIndex(['shift_id']);
            $table->dropIndex(['student_unique_id']);
            $table->dropIndex(['personal_number']);
            $table->dropIndex(['full_name_in_english_block_letter']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['expense_head_id']);
            $table->dropIndex(['expense_date']);
            $table->dropIndex(['amount']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('student_unique_ids', function (Blueprint $table) {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['serial']);
        });
    }
};