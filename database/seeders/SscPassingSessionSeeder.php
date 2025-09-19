<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SscPassingSession;

class SscPassingSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');

        // Last 20 sessions (e.g., 2005-2006 up to current year)
        for ($year = $currentYear - 19; $year < $currentYear; $year++) {
            $session = $year . '-' . ($year + 1);

            SscPassingSession::create([
                'session_name' => $session,
                'user_id' => 1
            ]);
        }
    }
}
