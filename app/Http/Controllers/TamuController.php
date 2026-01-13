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
            
            // Log info ukuran foto
            $sizeInMB = (strlen($base64Image) * 0.75) / (1024 * 1024);
            \Log::info('📸 Upload foto KTP', [
                'size_mb' => number_format($sizeInMB, 2),
                'nama_tamu' => $validated['nama_lengkap'],
            ]);
            
            // Validasi ukuran
            if ($sizeInMB > 5) {
                throw new \Exception('Ukuran foto terlalu besar (' . number_format($sizeInMB, 2) . ' MB). Maksimal 5MB.');
            }

            // Setup Cloudinary dengan SSL fix untuk Windows/WAMP
            // Disable SSL verification untuk development (putenv akan dipakai oleh Guzzle)
            if (config('app.env') === 'local') {
                putenv('CURLOPT_SSL_VERIFYPEER=0');
                \Log::info('🔓 SSL verification disabled (development mode)');
            }
            
            $cloudinaryConfig = [
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ]
            ];
            
            \Log::info('Cloudinary config check', [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key_set' => !empty(config('cloudinary.api_key')),
                'api_secret_set' => !empty(config('cloudinary.api_secret')),
            ]);
            
            $cloudinary = new Cloudinary($cloudinaryConfig);
            
            \Log::info('☁️ Mengirim ke Cloudinary...');
            
            $uploadResult = $cloudinary->uploadApi()->upload($base64Image, [
                'folder' => 'ktp_tamu',
                'resource_type' => 'image',
                'type' => 'private',
                'transformation' => [
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ]
            ]);
            
            \Log::info('✅ Upload ke Cloudinary berhasil', [
                'public_id' => $uploadResult['public_id'],
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
            
            \Log::error('❌ Error submit form tamu', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if (isset($uploadResult['public_id'])) {
                try {
                    $cloudinary->uploadApi()->destroy($uploadResult['public_id']);
                    \Log::info('🗑️ Cleanup: foto berhasil dihapus dari Cloudinary');
                } catch (\Exception $deleteError) {
                    \Log::error('⚠️ Gagal hapus foto dari Cloudinary');
                }
            }
            
            // Custom error message untuk masalah upload foto
            $errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            
            if (str_contains($e->getMessage(), 'Cloudinary') || 
                str_contains($e->getMessage(), 'upload') ||
                str_contains($e->getMessage(), 'Invalid image')) {
                $errorMessage = '❌ GAGAL UPLOAD FOTO\n\n';
                $errorMessage .= 'Error: ' . $e->getMessage() . '\n\n';
                $errorMessage .= 'Kemungkinan penyebab:\n';
                $errorMessage .= '• Koneksi internet tidak stabil\n';
                $errorMessage .= '• Ukuran foto terlalu besar\n';
                $errorMessage .= '• Format foto tidak valid\n';
                $errorMessage .= '• Kredensial Cloudinary bermasalah\n\n';
                $errorMessage .= 'Solusi: Coba ambil foto ulang atau refresh halaman.';
            }

            return back()->withErrors(['foto_error' => $errorMessage])->withInput();
        }
    }
}
