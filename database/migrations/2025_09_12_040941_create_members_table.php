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
            $table->string('name');
            $table->string('father_name');
            $table->string('mobile');
            $table->string('email')->unique();
            $table->string('nid_number')->unique();
            $table->string('picture')->nullable();
            $table->unsignedBigInteger('designation_id');
            $table->date('date_of_join');
            $table->unsignedBigInteger('branch_id');
            $table->text('present_address');
            $table->text('permanent_address');
            $table->string('unique_id')->unique();
            $table->unsignedBigInteger('introducer_id')->nullable();
            $table->unsignedBigInteger('religion_id');
            $table->string('nominee_name')->nullable();
            $table->string('nominee_relation')->nullable();
            $table->string('nominee_phone')->nullable();
            $table->string('temp_username')->unique();
            $table->string('temp_password');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('introducer_id')->references('id')->on('members')->onDelete('set null');
            $table->foreign('religion_id')->references('id')->on('religions')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
