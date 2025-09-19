<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Semester;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = [
            '1st Semester',
            '2nd Semester',
            '3rd Semester',
            '4th Semester',
            '5th Semester',
            '6th Semester',
            '7th Semester',
            '8th Semester',
            '9th Semester',
            '10th Semester',
            '11th Semester',
            '12th Semester',

        ];

        foreach ($semesters as $semester) {
            Semester::create(['semester_name' => $semester, 'user_id' => 1]);
        }
    }
}
