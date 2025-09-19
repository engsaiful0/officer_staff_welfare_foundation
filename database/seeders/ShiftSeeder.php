<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $shifts = [
            'First Shift',
            'Second Shift',
            'Day Shift',
            'Night Shift',
        ];

        foreach ($shifts as $shift) {
            Shift::create(['shift_name' => $shift,'user_id' => 1]);
        }
    }
}
