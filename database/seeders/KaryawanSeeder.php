<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawans = [
            [
                'nama_karyawan' => 'Budi Santoso',
                'email_karyawan' => 'budi.santoso@company.com',
                'departemen' => 'IT',
                'jabatan' => 'Supervisor',
            ],
            [
                'nama_karyawan' => 'Siti Nurhaliza',
                'email_karyawan' => 'siti.nurhaliza@company.com',
                'departemen' => 'Human Resources',
                'jabatan' => 'HR Manager',
            ],
            [
                'nama_karyawan' => 'Ahmad Dahlan',
                'email_karyawan' => 'ahmad.dahlan@company.com',
                'departemen' => 'Marketing',
                'jabatan' => 'Marketing Manager',
            ],
            [
                'nama_karyawan' => 'Dewi Lestari',
                'email_karyawan' => 'dewi.lestari@company.com',
                'departemen' => 'Finance',
                'jabatan' => 'Finance Manager',
            ],
            [
                'nama_karyawan' => 'Rudi Hartono',
                'email_karyawan' => 'rudi.hartono@company.com',
                'departemen' => 'IT',
                'jabatan' => 'Staff',
            ],
            [
                'nama_karyawan' => 'Rina Wijaya',
                'email_karyawan' => 'rina.wijaya@company.com',
                'departemen' => 'Operations',
                'jabatan' => 'Operations Manager',
            ],
            [
                'nama_karyawan' => 'Eko Prasetyo',
                'email_karyawan' => 'eko.prasetyo@company.com',
                'departemen' => 'General Affairs',
                'jabatan' => 'Staff',
            ],
        ];

        foreach ($karyawans as $karyawan) {
            Karyawan::create($karyawan);
        }
    }
}
