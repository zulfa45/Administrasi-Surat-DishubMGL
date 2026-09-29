<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@dishub.local',
            'password' => Hash::make('password'),
            'nip' => '198001012005011001',
            'jabatan' => 'Kepala Dinas',
            'department_id' => 1,
            'status' => 'active',
        ]);
        $admin->assignRole('admin');

        // Staf Loket
        $staf = User::create([
            'name' => 'Staf Loket',
            'email' => 'staf@dishub.local',
            'password' => Hash::make('password'),
            'nip' => '199001012015011002',
            'jabatan' => 'Staf Administrasi',
            'department_id' => 1,
            'status' => 'active',
        ]);
        $staf->assignRole('staf-loket');

        // Karyawan
        $karyawan = User::create([
            'name' => 'Karyawan',
            'email' => 'karyawan@dishub.local',
            'password' => Hash::make('password'),
            'nip' => '199501012020011003',
            'jabatan' => 'Analis Lalu Lintas',
            'department_id' => 2,
            'status' => 'active',
        ]);
        $karyawan->assignRole('karyawan');
    }
}
