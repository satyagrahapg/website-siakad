<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Semester;
use Carbon\Carbon;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Semester::insert([
            [
                'semester' => 'Semester 1',
                'tahun_ajaran' => '2023/2024',
                'status' => 0, // Inactive semester
                'start' => Carbon::parse('2023-08-01'),
                'end' => Carbon::parse('2023-12-15'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'semester' => 'Semester 2',
                'tahun_ajaran' => '2023/2024',
                'status' => 0, // Inactive semester
                'start' => Carbon::parse('2024-01-10'),
                'end' => Carbon::parse('2024-05-30'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'semester' => 'Semester 1',
                'tahun_ajaran' => '2024/2025',
                'status' => 0, // Inactive semester
                'start' => Carbon::parse('2024-07-08'),
                'end' => Carbon::parse('2024-12-20'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'semester' => 'Semester 2',
                'tahun_ajaran' => '2024/2025',
                'status' => 1, // Inactive semester
                'start' => Carbon::parse('2025-01-06'),
                'end' => Carbon::parse('2025-06-27'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
