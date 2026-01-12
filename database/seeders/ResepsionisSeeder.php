<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Resepsionis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResepsionisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat karyawan untuk resepsionis
        $karyawan = Karyawan::firstOrCreate(
            ['email_karyawan' => 'ani.suryani@company.com'],
            [
                'nama_karyawan' => 'Ani Suryani',
                'departemen' => 'General Affairs',
                'jabatan' => 'Resepsionis',
            ]
        );

        // Buat resepsionis berdasarkan karyawan tersebut
        Resepsionis::firstOrCreate(
            ['email_resepsionis' => $karyawan->email_karyawan],
            [
                'id_karyawan' => $karyawan->id_karyawan,
                'nama_resepsionis' => $karyawan->nama_karyawan,
                'password_resepsionis' => Hash::make('password123'),
            ]
        );

        // Buat karyawan untuk resepsionis kedua
        $karyawan2 = Karyawan::firstOrCreate(
            ['email_karyawan' => 'dontaskbskr@gmail.com'],
            [
                'nama_karyawan' => 'Budi Santoso',
                'departemen' => 'General Affairs',
                'jabatan' => 'Resepsionis',
            ]
        );

        // Buat resepsionis berdasarkan karyawan kedua
        Resepsionis::firstOrCreate(
            ['email_resepsionis' => $karyawan2->email_karyawan],
            [
                'id_karyawan' => $karyawan2->id_karyawan,
                'nama_resepsionis' => $karyawan2->nama_karyawan,
                'password_resepsionis' => Hash::make('password123'),
            ]
        );
    }
}
