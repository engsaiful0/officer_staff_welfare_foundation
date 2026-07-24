<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds product code to investment_types and seeds Bai-Muajjal + HPSM.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investment_types', function (Blueprint $table) {
            if (! Schema::hasColumn('investment_types', 'code')) {
                $table->string('code', 50)->nullable()->unique()->after('investment_type_name');
            }
        });

        $this->ensureType('Bai-Muajjal', 'bai_muajjal');
        $this->ensureType('HPSM', 'hpsm');
    }

    public function down(): void
    {
        Schema::table('investment_types', function (Blueprint $table) {
            if (Schema::hasColumn('investment_types', 'code')) {
                $table->dropUnique(['code']);
                $table->dropColumn('code');
            }
        });
    }

    private function ensureType(string $name, string $code): void
    {
        $existing = DB::table('investment_types')
            ->where('code', $code)
            ->orWhere('investment_type_name', $name)
            ->orWhere('investment_type_name', 'like', $name)
            ->first();

        if ($existing) {
            DB::table('investment_types')->where('id', $existing->id)->update([
                'investment_type_name' => $name,
                'code' => $code,
                'updated_at' => now(),
            ]);

            return;
        }

        // Match loosely named HPSM rows already in DB
        if ($code === 'hpsm') {
            $hpsm = DB::table('investment_types')
                ->whereRaw('UPPER(investment_type_name) LIKE ?', ['%HPSM%'])
                ->first();
            if ($hpsm) {
                DB::table('investment_types')->where('id', $hpsm->id)->update([
                    'investment_type_name' => $name,
                    'code' => $code,
                    'updated_at' => now(),
                ]);

                return;
            }
        }

        DB::table('investment_types')->insert([
            'investment_type_name' => $name,
            'code' => $code,
            'user_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
