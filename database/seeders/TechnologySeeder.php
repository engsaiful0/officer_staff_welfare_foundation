<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Technology;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $technologies = [
            'CMT',
            'CCE',
            'ET',
            'CE',
            'ME',
            'PT',
            'Architecture',
            'TE',
            'IPE',
            'CSE',
            'EEE',
        ];

        foreach ($technologies as $technology) {
            Technology::create(['technology_name' => $technology,'user_id' => 1]);
        }
    }
}
