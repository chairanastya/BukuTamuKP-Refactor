<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Tamu;
use App\Models\Kunjungan;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TamuController extends Controller
{
    public function showForm()
    {
        return view('tamu.form');
    }

    public function searchKaryawan(Request $request)
    {
        $keyword = $request->input('q', '');

        if (empty($keyword)) {
            return response()->json([]);
        }

        $karyawans = Karyawan::where('nama_karyawan', 'ILIKE', '%' . $keyword . '%')
            ->orderBy('nama_karyawan', 'asc')
            ->get(['id_karyawan', 'nama_karyawan', 'jabatan', 'departemen']);

        return response()->json($karyawans);
    }

    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'instansi' => 'nullable|string|max:255',
            'tujuan' => 'required|string',
            'foto_ktp' => 'required|string',
            'karyawan_ids' => 'required|string',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'tujuan.required' => 'Tujuan kedatangan wajib diisi',
            'foto_ktp.required' => 'Foto KTP wajib diambil',
            'karyawan_ids.required' => 'Minimal pilih 1 karyawan yang dituju',
        ]);

        try {
            DB::beginTransaction();

            $base64Image = $validated['foto_ktp'];

            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ]
            ]);
            $uploadResult = $cloudinary->uploadApi()->upload($base64Image, [
                'folder' => 'ktp_tamu',
                'resource_type' => 'image',
                'type' => 'private',
                'transformation' => [
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ]
            ]);

            $tamu = Tamu::create([
                'nama_tamu' => $validated['nama_lengkap'],
                'email_tamu' => $validated['email'],
                'instansi_tamu' => $validated['instansi'],
                'ktp_public_id' => $uploadResult['public_id'],
            ]);

            $kunjungan = Kunjungan::create([
                'id_tamu' => $tamu->id_tamu,
                'tujuan_kunjungan' => $validated['tujuan'],
                'tanggal_kunjungan' => now()->toDateString(),
                'jam_mulai' => now()->toTimeString(),
                'status' => 'pending',
            ]);

            $karyawanIds = json_decode($validated['karyawan_ids'], true);

            if (!empty($karyawanIds)) {
                foreach ($karyawanIds as $karyawanId) {
                    $kunjungan->karyawan()->attach($karyawanId);
                }
            }

            DB::commit();

            // Generate token approval & kirim email (nanti)

            return redirect()->route('tamu.form')->with('success', 'Data kunjungan berhasil dikirim! Silakan tunggu approval dari karyawan.');

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($uploadResult['public_id'])) {
                try {
                    $cloudinary->uploadApi()->destroy($uploadResult['public_id']);
                } catch (\Exception $deleteError) {
                }
            }

            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}
