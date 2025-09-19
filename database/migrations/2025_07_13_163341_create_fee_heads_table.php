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
        Schema::create('fee_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('semester_id')->nullable()->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('month_id')->nullable();
            $table->foreign('month_id')->references('id')->on('months')->onDelete('cascade');
            $table->string("fee_type")->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('is_discountable')->default('No');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['name', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_heads');
    }
};
