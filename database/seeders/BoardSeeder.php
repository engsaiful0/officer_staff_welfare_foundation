<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Board;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $boards = [
            'BTEB',
            'Chittagong',
            'Dhaka',
            'Rajshahi',
            'Comilla',
            'Jessore',
            'Sylhet',
            'Barisal',
            'Mymensingh',
            'Dinajpur',
            'Technical',
        ];

        foreach ($boards as $board) {
            Board::create(['board_name' => $board,'user_id' => 1]);
        }
    }
}
