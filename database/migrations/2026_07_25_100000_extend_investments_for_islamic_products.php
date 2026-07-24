<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends investments for Islamic products (Bai-Muajjal / HPSM).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            if (! Schema::hasColumn('investments', 'investment_type_id')) {
                $table->unsignedBigInteger('investment_type_id')->nullable()->after('member_id');
                $table->foreign('investment_type_id')
                    ->references('id')
                    ->on('investment_types')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('investments', 'calculation_method')) {
                $table->string('calculation_method', 32)->nullable()->after('product_name');
            }

            if (! Schema::hasColumn('investments', 'selling_price')) {
                $table->decimal('selling_price', 15, 2)->nullable()->after('principal_amount');
            }

            if (! Schema::hasColumn('investments', 'profit_amount')) {
                $table->decimal('profit_amount', 15, 2)->nullable()->after('selling_price');
            }

            if (! Schema::hasColumn('investments', 'emi_amount')) {
                $table->decimal('emi_amount', 15, 2)->nullable()->after('profit_amount');
            }

            if (! Schema::hasColumn('investments', 'remaining_principal')) {
                $table->decimal('remaining_principal', 15, 2)->nullable()->after('emi_amount');
            }

            if (! Schema::hasColumn('investments', 'ownership_ratio')) {
                $table->decimal('ownership_ratio', 8, 4)->nullable()->after('remaining_principal');
            }

            if (! Schema::hasColumn('investments', 'account_opening_date')) {
                $table->date('account_opening_date')->nullable()->after('start_date');
            }

            if (! Schema::hasColumn('investments', 'gestation_date')) {
                $table->date('gestation_date')->nullable()->after('account_opening_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            if (Schema::hasColumn('investments', 'investment_type_id')) {
                $table->dropForeign(['investment_type_id']);
                $table->dropColumn('investment_type_id');
            }

            $columns = [
                'calculation_method',
                'selling_price',
                'profit_amount',
                'emi_amount',
                'remaining_principal',
                'ownership_ratio',
                'account_opening_date',
                'gestation_date',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('investments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
