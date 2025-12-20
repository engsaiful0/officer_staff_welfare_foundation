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
        Schema::create('monthly_deposit_collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deposit_id');
            $table->unsignedBigInteger('member_id');
            $table->string('collection_number')->unique();
            $table->date('collection_date');
            $table->decimal('amount', 15, 2);
            $table->string('month')->nullable(); // e.g., "2024-01" or "January 2024"
            $table->text('description')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('deposit_id')->references('id')->on('deposits')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            
            // Indexes
            $table->index(['deposit_id', 'collection_date']);
            $table->index(['member_id', 'collection_date']);
            $table->index('collection_date');
            $table->index('month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_deposit_collections');
    }
};
