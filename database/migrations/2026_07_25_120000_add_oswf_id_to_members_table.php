<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'oswf_id')) {
                $table->string('oswf_id', 50)->nullable()->unique()->after('employees_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'oswf_id')) {
                $table->dropUnique(['oswf_id']);
                $table->dropColumn('oswf_id');
            }
        });
    }
};
