<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'first_guarantor_name')) {
                $table->string('first_guarantor_name')->nullable()->after('nominee_permanent_address');
                $table->string('first_guarantor_employees_id', 50)->nullable()->after('first_guarantor_name');
                $table->foreignId('first_guarantor_designation_id')
                    ->nullable()
                    ->after('first_guarantor_employees_id')
                    ->constrained('designations')
                    ->nullOnDelete();
                $table->string('first_guarantor_branch_name')->nullable()->after('first_guarantor_designation_id');
                $table->date('first_guarantor_date_of_birth')->nullable()->after('first_guarantor_branch_name');
                $table->date('first_guarantor_date_of_joining')->nullable()->after('first_guarantor_date_of_birth');
                $table->string('first_guarantor_mobile', 15)->nullable()->after('first_guarantor_date_of_joining');

                $table->string('second_guarantor_name')->nullable()->after('first_guarantor_mobile');
                $table->string('second_guarantor_employees_id', 50)->nullable()->after('second_guarantor_name');
                $table->foreignId('second_guarantor_designation_id')
                    ->nullable()
                    ->after('second_guarantor_employees_id')
                    ->constrained('designations')
                    ->nullOnDelete();
                $table->string('second_guarantor_branch_name')->nullable()->after('second_guarantor_designation_id');
                $table->date('second_guarantor_date_of_birth')->nullable()->after('second_guarantor_branch_name');
                $table->date('second_guarantor_date_of_joining')->nullable()->after('second_guarantor_date_of_birth');
                $table->string('second_guarantor_mobile', 15)->nullable()->after('second_guarantor_date_of_joining');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $columns = [
                'first_guarantor_name',
                'first_guarantor_employees_id',
                'first_guarantor_designation_id',
                'first_guarantor_branch_name',
                'first_guarantor_date_of_birth',
                'first_guarantor_date_of_joining',
                'first_guarantor_mobile',
                'second_guarantor_name',
                'second_guarantor_employees_id',
                'second_guarantor_designation_id',
                'second_guarantor_branch_name',
                'second_guarantor_date_of_birth',
                'second_guarantor_date_of_joining',
                'second_guarantor_mobile',
            ];

            if (Schema::hasColumn('members', 'first_guarantor_designation_id')) {
                $table->dropConstrainedForeignId('first_guarantor_designation_id');
            }
            if (Schema::hasColumn('members', 'second_guarantor_designation_id')) {
                $table->dropConstrainedForeignId('second_guarantor_designation_id');
            }

            $existing = array_values(array_filter($columns, function ($col) {
                return ! str_ends_with($col, '_designation_id') && Schema::hasColumn('members', $col);
            }));
            if ($existing !== []) {
                $table->dropColumn($existing);
            }
        });
    }
};
