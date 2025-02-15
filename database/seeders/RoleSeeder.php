<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = Role::create(['name' => 'Super Admin']);
        $admin = Role::create(['name' => 'Admin']);
        $wali_kelas = Role::create(['name' => 'Wali Kelas']);
        $guru = Role::create(['name' => 'Guru']);
        $siswa = Role::create(['name' => 'Siswa']);

        $admin->givePermissionTo([
            'create-user',
            'edit-user',
            'delete-user',
            'view-user'
        ]);

        $wali_kelas->givePermissionTo([
            'view-user'
        ]);

        $guru->givePermissionTo([
            'view-user'
        ]);

        $siswa->givePermissionTo([
            'view-user'
        ]);
    }
}