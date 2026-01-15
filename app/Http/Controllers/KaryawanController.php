<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function createKaryawan()
    {
        return view('resepsionis.create-karyawan');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'email_karyawan' => 'required|email|max:255|unique:karyawan,email_karyawan',
            'departemen' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
        ], [
            'nama_karyawan.required' => 'Nama karyawan wajib diisi',
            'email_karyawan.required' => 'Email karyawan wajib diisi',
            'email_karyawan.email' => 'Format email tidak valid',
            'email_karyawan.unique' => 'Email sudah terdaftar',
            'departemen.required' => 'Departemen wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
        ]);

        try {
            Karyawan::create($validated);

            return redirect()->route('resepsionis.karyawan')
                ->with('success', 'Karyawan berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menambahkan karyawan: ' . $e->getMessage()]);
        }
    }

    public function searchDepartemen(Request $request)
    {
        $keyword = $request->input('q', '');

        if (empty($keyword)) {
            return response()->json([]);
        }

        $departemens = Karyawan::select('departemen')
            ->where('departemen', 'ILIKE', '%' . $keyword . '%')
            ->distinct()
            ->orderBy('departemen', 'asc')
            ->limit(10)
            ->pluck('departemen');

        return response()->json($departemens);
    }

    public function searchJabatan(Request $request)
    {
        $keyword = $request->input('q', '');

        if (empty($keyword)) {
            return response()->json([]);
        }

        $jabatans = Karyawan::select('jabatan')
            ->where('jabatan', 'ILIKE', '%' . $keyword . '%')
            ->distinct()
            ->orderBy('jabatan', 'asc')
            ->limit(10)
            ->pluck('jabatan');

        return response()->json($jabatans);
    }
}
