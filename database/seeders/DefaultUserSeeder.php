<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating Super Admin User
        $superadmin = User::create([
            'name' => 'Satyagraha Pakarti Gemilang',
            'email' => 'gilan.lan8@gmail.com',
            'username' => 'satyagraha',
            'password' => 'satyagraha'
        ]);
        $superadmin->assignRole(['Super Admin', 'Admin', 'Guru', 'Wali Kelas']);
    }
}