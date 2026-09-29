<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Sekretariat',
                'description' => 'Bagian Sekretariat Dinas Perhubungan',
                'status' => 'active',
            ],
            [
                'name' => 'Bidang Lalu Lintas',
                'description' => 'Bidang yang mengurus lalu lintas',
                'status' => 'active',
            ],
            [
                'name' => 'Bidang Angkutan',
                'description' => 'Bidang yang mengurus angkutan jalan',
                'status' => 'active',
            ],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
