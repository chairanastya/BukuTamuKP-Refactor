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

        $query = Karyawan::select('departemen')
            ->whereNotNull('departemen')
            ->distinct()
            ->orderBy('departemen', 'asc');

        if (!empty($keyword)) {
            $query->where('departemen', 'ILIKE', '%' . $keyword . '%');
        }

        $departemens = $query->pluck('departemen');

        return response()->json($departemens);
    }

    public function searchJabatan(Request $request)
    {
        $keyword = $request->input('q', '');

        $query = Karyawan::select('jabatan')
            ->whereNotNull('jabatan')
            ->distinct()
            ->orderBy('jabatan', 'asc');

        if (!empty($keyword)) {
            $query->where('jabatan', 'ILIKE', '%' . $keyword . '%');
        }

        $jabatans = $query->pluck('jabatan');

        return response()->json($jabatans);
    }

    public function destroy($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);
            
            // Check if karyawan is linked to any kunjungan
            $hasKunjungan = $karyawan->kunjungan()->exists();
            
            if ($hasKunjungan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus karyawan yang memiliki riwayat kunjungan'
                ], 400);
            }
            
            $karyawan->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan: ' . $e->getMessage()
            ], 500);
        }
    }
}
