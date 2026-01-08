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
        $karyawan = Karyawan::create([
            'nama_karyawan' => 'Ani Suryani',
            'email_karyawan' => 'ani.suryani@company.com',
            'departemen' => 'General Affairs',
            'jabatan' => 'Resepsionis',
        ]);

        // Buat resepsionis berdasarkan karyawan tersebut
        Resepsionis::create([
            'id_karyawan' => $karyawan->id_karyawan,
            'nama_resepsionis' => $karyawan->nama_karyawan,
            'email_resepsionis' => $karyawan->email_karyawan,
            'password_resepsionis' => Hash::make('password123'),
        ]);
    }
}
