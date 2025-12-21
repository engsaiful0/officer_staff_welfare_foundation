<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            // Add deposit_type_id column
            $table->unsignedBigInteger('deposit_type_id')->nullable()->after('rate');
            
            // Add foreign key constraint
            $table->foreign('deposit_type_id')->references('id')->on('deposit_types')->onDelete('set null');
            
            // Add index
            $table->index('deposit_type_id');
        });

        // Migrate existing data if possible (optional - you may want to create default deposit types first)
        // This assumes you have deposit types with names matching the enum values
        // You can comment this out if you want to handle migration manually
        /*
        $savingsType = DB::table('deposit_types')->where('deposit_type_name', 'Savings')->first();
        $fixedType = DB::table('deposit_types')->where('deposit_type_name', 'Fixed Deposit')->first();
        $recurringType = DB::table('deposit_types')->where('deposit_type_name', 'Recurring Deposit')->first();
        
        if ($savingsType) {
            DB::table('deposits')->where('deposit_type', 'savings')->update(['deposit_type_id' => $savingsType->id]);
        }
        if ($fixedType) {
            DB::table('deposits')->where('deposit_type', 'fixed')->update(['deposit_type_id' => $fixedType->id]);
        }
        if ($recurringType) {
            DB::table('deposits')->where('deposit_type', 'recurring')->update(['deposit_type_id' => $recurringType->id]);
        }
        */

        Schema::table('deposits', function (Blueprint $table) {
            // Drop the old enum column and its index
            $table->dropIndex(['deposit_type']);
            $table->dropColumn('deposit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            // Add back the enum column
            $table->enum('deposit_type', ['savings', 'fixed', 'recurring'])->after('rate');
            $table->index('deposit_type');
        });

        // Migrate data back (optional)
        /*
        DB::table('deposits')->whereNotNull('deposit_type_id')->each(function ($deposit) {
            $depositType = DB::table('deposit_types')->find($deposit->deposit_type_id);
            if ($depositType) {
                $typeName = strtolower(str_replace(' ', '_', $depositType->deposit_type_name));
                if (in_array($typeName, ['savings', 'fixed', 'recurring'])) {
                    DB::table('deposits')->where('id', $deposit->id)->update(['deposit_type' => $typeName]);
                }
            }
        });
        */

        Schema::table('deposits', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['deposit_type_id']);
            $table->dropIndex(['deposit_type_id']);
            $table->dropColumn('deposit_type_id');
        });
    }
};
