<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quards', function (Blueprint $table) {
            if (!Schema::hasColumn('quards', 'total_installment_amount')) {
                $table->decimal('total_installment_amount', 15, 2)->default(0)->after('installment_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quards', function (Blueprint $table) {
            if (Schema::hasColumn('quards', 'total_installment_amount')) {
                $table->dropColumn('total_installment_amount');
            }
        });
    }
};

