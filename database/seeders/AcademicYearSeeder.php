<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        $years = range($currentYear, $currentYear - 19); // last 20 years

        foreach ($years as $year) {
            AcademicYear::create([
                'academic_year_name' => $year,
                'user_id' => 1
            ]);
        }
    }
}
