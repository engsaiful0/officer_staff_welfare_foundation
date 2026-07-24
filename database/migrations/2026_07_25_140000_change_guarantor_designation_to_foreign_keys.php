<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'first_guarantor_designation')) {
                $table->dropColumn('first_guarantor_designation');
            }
            if (Schema::hasColumn('members', 'second_guarantor_designation')) {
                $table->dropColumn('second_guarantor_designation');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'first_guarantor_designation_id')) {
                $table->foreignId('first_guarantor_designation_id')
                    ->nullable()
                    ->after('first_guarantor_employees_id')
                    ->constrained('designations')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('members', 'second_guarantor_designation_id')) {
                $table->foreignId('second_guarantor_designation_id')
                    ->nullable()
                    ->after('second_guarantor_employees_id')
                    ->constrained('designations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'first_guarantor_designation_id')) {
                $table->dropConstrainedForeignId('first_guarantor_designation_id');
            }
            if (Schema::hasColumn('members', 'second_guarantor_designation_id')) {
                $table->dropConstrainedForeignId('second_guarantor_designation_id');
            }
        });

        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'first_guarantor_designation')) {
                $table->string('first_guarantor_designation')->nullable()->after('first_guarantor_employees_id');
            }
            if (! Schema::hasColumn('members', 'second_guarantor_designation')) {
                $table->string('second_guarantor_designation')->nullable()->after('second_guarantor_employees_id');
            }
        });
    }
};
