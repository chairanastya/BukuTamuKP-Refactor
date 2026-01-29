<?php

namespace App\Http\Controllers;

use App\Jobs\SendConfirmationEmailJob;
use App\Jobs\UploadPhotoToCloudinaryJob;
use App\Models\Karyawan;
use App\Models\Tamu;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

        $karyawans = Karyawan::where('status', 'aktif')
            ->where('nama_karyawan', 'ILIKE', '%' . $keyword . '%')
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

            // Validasi ukuran foto
            $sizeInMB = (strlen($base64Image) * 0.75) / (1024 * 1024);
            Log::info('Form submission', [
                'size_mb' => number_format($sizeInMB, 2),
                'nama_tamu' => $validated['nama_lengkap'],
                'photo_size_valid' => $sizeInMB <= 5,
            ]);

            if ($sizeInMB > 5) {
                throw new \Exception('Ukuran foto terlalu besar (' . number_format($sizeInMB, 2) . ' MB). Maksimal 5MB.');
            }

            // Create Tamu record WITHOUT ktp_public_id (will be filled by job)
            $tamu = Tamu::create([
                'nama_tamu' => $validated['nama_lengkap'],
                'email_tamu' => $validated['email'],
                'instansi_tamu' => $validated['instansi'],
                'ktp_public_id' => null, // Will be updated by UploadPhotoToCloudinaryJob
            ]);

            Log::info('Tamu berhasil dibuat', ['id_tamu' => $tamu->id_tamu]);

            // Generate token untuk approval
            $token = Str::random(64);
            $expiredAt = now()->addHours(24);

            $kunjungan = Kunjungan::create([
                'id_tamu' => $tamu->id_tamu,
                'tujuan_kunjungan' => $validated['tujuan'],
                'tanggal_kunjungan' => now()->toDateString(),
                'jam_mulai' => now()->toTimeString(),
                'status' => 'pending',
                'token_approval' => $token,
                'expired_at' => $expiredAt,
            ]);

            Log::info('Kunjungan berhasil dibuat', ['id_kunjungan' => $kunjungan->id_kunjungan]);

            $karyawanIds = json_decode($validated['karyawan_ids'], true);

            if (!empty($karyawanIds)) {
                foreach ($karyawanIds as $karyawanId) {
                    $kunjungan->karyawan()->attach($karyawanId);
                }
                Log::info('Karyawan attached', ['count' => count($karyawanIds)]);
            }

            DB::commit();

            // Dispatch Cloudinary upload job FIRST (no blocking)
            Log::info('Dispatching Cloudinary upload job', ['tamu_id' => $tamu->id_tamu]);
            dispatch(new UploadPhotoToCloudinaryJob($base64Image, $tamu->id_tamu));

            // Dispatch confirmation email jobs per karyawan (no blocking)
            Log::info('Dispatching confirmation email jobs', [
                'karyawan_ids_count' => count($karyawanIds),
                'kunjungan_id' => $kunjungan->id_kunjungan,
            ]);

            foreach ($karyawanIds as $karyawanId) {
                dispatch(new SendConfirmationEmailJob($karyawanId, $tamu, $kunjungan, $token));
            }

            Log::info('All jobs dispatched successfully');

            $successMessage = 'Data kunjungan berhasil dikirim! Email konfirmasi sedang dikirim ke karyawan. Silakan tunggu approval dari karyawan.';

            if (auth('resepsionis')->check()) {
                return redirect()->route('resepsionis.kunjungan.create')->with('success', $successMessage);
            }

            return redirect()->route('tamu.form')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error submit form tamu', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();

            if (
                str_contains($e->getMessage(), 'foto') ||
                str_contains($e->getMessage(), 'size') ||
                str_contains($e->getMessage(), 'MB')
            ) {
                $errorMessage = 'GAGAL UPLOAD FOTO\n\n';
                $errorMessage .= 'Error: ' . $e->getMessage() . '\n\n';
                $errorMessage .= 'Kemungkinan penyebab:\n';
                $errorMessage .= '• Ukuran foto terlalu besar\n';
                $errorMessage .= '• Format foto tidak valid\n\n';
                $errorMessage .= 'Solusi: Coba ambil foto ulang atau refresh halaman.';
            }

            return back()->withErrors(['foto_error' => $errorMessage])->withInput();
        }
    }
}
