<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeeSettings;

class FeeSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default fee settings
        FeeSettings::create([
            'monthly_fee_amount' => 5000.00,
            'payment_deadline_day' => 10,
            'fine_amount_per_day' => 50.00,
            'maximum_fine_amount' => 1000.00,
            'is_percentage_fine' => false,
            'fine_percentage' => null,
            'grace_period_days' => 0,
            'is_active' => true,
            'notes' => 'Default monthly fee settings - Students must pay 5000 BDT by 10th of each month with 50 BDT fine per day after deadline (max 1000 BDT fine)',
        ]);
    }
}
