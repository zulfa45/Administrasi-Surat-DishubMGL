<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'manage-users',
            'manage-departments',
            'manage-letters',
            'create-letter',
            'edit-letter',
            'delete-letter',
            'create-assignment',
            'view-assignment',
            'update-assignment',
            'view-reports',
            'print-disposition',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $stafLoket = Role::firstOrCreate(['name' => 'staf-loket']);
        $stafLoket->givePermissionTo([
            'create-letter',
            'edit-letter',
            'delete-letter',
            'create-assignment',
            'view-assignment',
            'view-reports',
            'print-disposition',
        ]);

        $karyawan = Role::firstOrCreate(['name' => 'karyawan']);
        $karyawan->givePermissionTo([
            'view-assignment',
            'update-assignment',
        ]);
    }
}
