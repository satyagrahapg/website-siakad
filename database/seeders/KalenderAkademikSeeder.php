<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KalenderAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('kalender_akademiks')->insert([
        //     [
        //         'title' => 'Semester Start',
        //         'tipe_kegiatan' => 1, // Adjust this to match your logic for 'tipe_kegiatan'
        //         'start' => Carbon::parse('2024-10-01'),
        //         'end' => Carbon::parse('2024-10-02'),
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        //     [
        //         'title' => 'Midterm Exams',
        //         'tipe_kegiatan' => 2,
        //         'start' => Carbon::parse('2024-10-15'),
        //         'end' => Carbon::parse('2024-10-20'),
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        //     [
        //         'title' => 'Semester End',
        //         'tipe_kegiatan' => 3,
        //         'start' => Carbon::parse('2024-10-12'),
        //         'end' => Carbon::parse('2024-10-14'),
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ],
        // ]);
    }
}
