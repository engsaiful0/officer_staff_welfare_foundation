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
        Schema::create('investment_account_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->integer('serial');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('investment_account_id')->nullable();
            $table->year('year')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('investment_account_id')->references('id')->on('investment_accounts')->onDelete('set null');
            
            // Indexes
            $table->index('account_number');
            $table->index('serial');
            $table->index(['year', 'serial']);
            $table->index('user_id');
            $table->index('investment_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_account_numbers');
    }
};
