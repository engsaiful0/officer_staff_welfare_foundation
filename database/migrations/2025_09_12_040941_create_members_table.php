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
        Schema::create('members', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

            $table->string('name');
            $table->string('father_name');
            $table->string('mother_name');

            $table->date('date_of_birth');

            $table->string('spouse_name');

            $table->string('mobile')->unique();

            $table->string('email')->nullable()->unique();

            $table->string('nid_number')->nullable()->unique();

            $table->string('picture')->nullable();

            $table->text('present_address');

            $table->text('permanent_address');

            $table->foreignId('religion_id')
                ->constrained('religions')
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Professional Information
            |--------------------------------------------------------------------------
            */

            $table->foreignId('designation_id')
                ->constrained('designations')
                ->restrictOnDelete();

            $table->date('date_of_join_in_ibbl');

            $table->foreignId('branch_id')
                ->constrained('branches')
                ->restrictOnDelete();

            $table->string('status')->default('ACTIVE');

            $table->string('employees_id')->unique();

            /** OSWF membership ID (manual / office-assigned) */
            $table->string('oswf_id', 50)->nullable()->unique();

            /** Auto-generated system ID (e.g. MEM000123) — used across deposits, HPSM, etc. */
            $table->string('unique_id')->nullable()->unique();

            /** Manual / office Member ID (also tracked in member_unique_ids when assigned) */
            $table->string('member_unique_id')->nullable()->unique();

            $table->unsignedInteger('serial')->nullable();

            $table->string('diposit_account_number')->nullable();

            $table->date('account_opening_date');

            /*
            |--------------------------------------------------------------------------
            | Nominee Information
            |--------------------------------------------------------------------------
            */

            $table->string('nominee_name')->nullable();

            $table->string('nominee_father_name')->nullable();

            $table->string('nominee_mother_name')->nullable();

            $table->string('nominee_spouse_name')->nullable();

            $table->foreignId('nominee_relation_id')
                ->nullable()
                ->constrained('relations')
                ->nullOnDelete();

            $table->string('nominee_phone')->nullable();

            $table->string('nominee_nid_number')->nullable();

            $table->date('nominee_date_of_birth')->nullable();

            $table->string('nominee_picture')->nullable();

            $table->text('nominee_present_address')->nullable();

            $table->text('nominee_permanent_address')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Guarantor Information
            |--------------------------------------------------------------------------
            */

            $table->string('first_guarantor_name')->nullable();
            $table->string('first_guarantor_employees_id', 50)->nullable();
            $table->foreignId('first_guarantor_designation_id')
                ->nullable()
                ->constrained('designations')
                ->nullOnDelete();
            $table->string('first_guarantor_branch_name')->nullable();
            $table->date('first_guarantor_date_of_birth')->nullable();
            $table->date('first_guarantor_date_of_joining')->nullable();
            $table->string('first_guarantor_mobile', 15)->nullable();

            $table->string('second_guarantor_name')->nullable();
            $table->string('second_guarantor_employees_id', 50)->nullable();
            $table->foreignId('second_guarantor_designation_id')
                ->nullable()
                ->constrained('designations')
                ->nullOnDelete();
            $table->string('second_guarantor_branch_name')->nullable();
            $table->date('second_guarantor_date_of_birth')->nullable();
            $table->date('second_guarantor_date_of_joining')->nullable();
            $table->string('second_guarantor_mobile', 15)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Optional System Fields
            |--------------------------------------------------------------------------
            */

            $table->foreignId('introducer_id')
                ->nullable()
                ->constrained('members')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('temp_username')->nullable()->unique();

            $table->string('temp_password')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};