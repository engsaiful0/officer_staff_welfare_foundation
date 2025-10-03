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
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('file_path');
            $table->unsignedBigInteger('imported_by');
            $table->timestamp('imported_at');
            $table->integer('rows_imported')->default(0);
            $table->json('errors')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('imported_by')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('imported_at');
            $table->index('imported_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
