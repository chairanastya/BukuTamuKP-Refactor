<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Karyawan;
use App\Models\Tamu;
use App\Models\Dokumentasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\KunjunganNotification;
use App\Mail\NotulensiRequest;
use Cloudinary\Cloudinary;

class ResepsionisController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();

        $stats = [
            'total' => Kunjungan::whereDate('tanggal_kunjungan', $today)->count(),
            'pending' => Kunjungan::whereDate('tanggal_kunjungan', $today)->where('status', 'pending')->count(),
            'done' => Kunjungan::whereDate('tanggal_kunjungan', $today)->where('status', 'done')->count(),
            'canceled' => Kunjungan::whereDate('tanggal_kunjungan', $today)->where('status', 'canceled')->count(),
        ];

        $allTimeStats = [
            'total' => Kunjungan::count(),
            'pending' => Kunjungan::where('status', 'pending')->count(),
            'done' => Kunjungan::where('status', 'done')->count(),
            'canceled' => Kunjungan::where('status', 'canceled')->count(),
        ];

        return view('resepsionis.dashboard', compact('stats', 'allTimeStats'));
    }

    public function getKunjunganData(Request $request)
    {
        $query = Kunjungan::with(['tamu', 'karyawan'])
            ->whereDate('tanggal_kunjungan', now()->toDateString())
            ->orderBy('tanggal_kunjungan', 'desc')
            ->orderBy('jam_mulai', 'desc');

        $kunjungans = $query->get()->map(function ($kunjungan) {
            return [
                'id_kunjungan' => $kunjungan->id_kunjungan,
                'id_tamu' => $kunjungan->tamu->id_tamu ?? null,
                'ktp_token' => $kunjungan->tamu->ktp_access_token ?? null,
                'tanggal' => $kunjungan->tanggal_kunjungan->format('d/m/Y'),
                'jam' => substr($kunjungan->jam_mulai, 0, 5) . ' - ' . substr($kunjungan->jam_selesai ?? '00:00', 0, 5),
                'nama_tamu' => $kunjungan->tamu->nama_tamu ?? '-',
                'email_tamu' => $kunjungan->tamu->email_tamu ?? '-',
                'has_ktp' => !empty($kunjungan->tamu->ktp_public_id),
                'instansi' => $kunjungan->tamu->instansi_tamu ?? '-',
                'karyawan' => $kunjungan->karyawan->map(function ($k) {
                    return [
                        'nama' => $k->nama_karyawan,
                        'jabatan' => $k->jabatan ?? '-',
                        'departemen' => $k->departemen ?? '-',
                    ];
                }),
                'tujuan_kunjungan' => $kunjungan->tujuan_kunjungan ?? '-',
                'status' => $kunjungan->status,
                'alasan_batal' => $kunjungan->alasan_batal,
            ];
        });

        return response()->json(['data' => $kunjungans]);
    }

    public function getRiwayatData(Request $request)
    {
        $query = Kunjungan::with(['tamu', 'karyawan'])
            ->orderBy('tanggal_kunjungan', 'desc')
            ->orderBy('jam_mulai', 'desc');

        $kunjungans = $query->get()->map(function ($kunjungan) {
            return [
                'id_kunjungan' => $kunjungan->id_kunjungan,
                'id_tamu' => $kunjungan->tamu->id_tamu ?? null,
                'ktp_token' => $kunjungan->tamu->ktp_access_token ?? null,
                'tanggal' => $kunjungan->tanggal_kunjungan->format('d/m/Y'),
                'jam' => substr($kunjungan->jam_mulai, 0, 5) . ' - ' . substr($kunjungan->jam_selesai ?? '00:00', 0, 5),
                'nama_tamu' => $kunjungan->tamu->nama_tamu ?? '-',
                'email_tamu' => $kunjungan->tamu->email_tamu ?? '-',
                'has_ktp' => !empty($kunjungan->tamu->ktp_public_id),
                'instansi' => $kunjungan->tamu->instansi_tamu ?? '-',
                'karyawan' => $kunjungan->karyawan->map(function ($k) {
                    return [
                        'nama' => $k->nama_karyawan,
                        'jabatan' => $k->jabatan ?? '-',
                        'departemen' => $k->departemen ?? '-',
                    ];
                }),
                'tujuan_kunjungan' => $kunjungan->tujuan_kunjungan ?? '-',
                'status' => $kunjungan->status,
                'alasan_batal' => $kunjungan->alasan_batal,
            ];
        });

        return response()->json(['data' => $kunjungans]);
    }

    public function acceptKunjungan(Request $request, $id)
    {
        $kunjungan = Kunjungan::findOrFail($id);

        if ($kunjungan->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Kunjungan sudah diproses'], 400);
        }

        $kunjungan->update(['status' => 'approved']);

        Log::info('Kunjungan diterima (approved), ID: ' . $kunjungan->id_kunjungan);

        // Send notification to guest (tamu)
        $this->sendNotificationToTamu($kunjungan, 'diterima');

        // Send notulensi request to all karyawan involved in the visit
        $this->sendNotulensiRequestToKaryawan($kunjungan);

        return response()->json(['success' => true, 'message' => 'Kunjungan berhasil diterima']);
    }

    public function rejectKunjungan(Request $request, $id)
    {
        $request->validate([
            'alasan_batal' => 'required|string|max:500',
        ]);

        $kunjungan = Kunjungan::findOrFail($id);

        if ($kunjungan->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Kunjungan sudah diproses'], 400);
        }

        $kunjungan->update([
            'status' => 'canceled',
            'alasan_batal' => $request->alasan_batal,
        ]);

        Log::info('Kunjungan ditolak (canceled), ID: ' . $kunjungan->id_kunjungan);

        $this->sendNotificationToTamu($kunjungan, 'ditolak');

        return response()->json(['success' => true, 'message' => 'Kunjungan berhasil ditolak']);
    }

    public function createKunjungan()
    {
        return view('resepsionis.create-kunjungan');
    }

    public function riwayat()
    {
        $allTimeStats = [
            'total' => Kunjungan::count(),
            'pending' => Kunjungan::where('status', 'pending')->count(),
            'done' => Kunjungan::where('status', 'done')->count(),
            'canceled' => Kunjungan::where('status', 'canceled')->count(),
        ];

        return view('resepsionis.riwayat', compact('allTimeStats'));
    }

    public function daftarKaryawan()
    {
        $stats = [
            'total' => Karyawan::count(),
            'departemen' => Karyawan::distinct('departemen')->count('departemen'),
        ];

        return view('resepsionis.karyawan', compact('stats'));
    }

    public function getKaryawanData(Request $request)
    {
        $karyawans = Karyawan::orderBy('nama_karyawan')
            ->get()
            ->map(function ($karyawan) {
                return [
                    'id_karyawan' => $karyawan->id_karyawan,
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'email_karyawan' => $karyawan->email_karyawan,
                    'departemen' => $karyawan->departemen ?? '-',
                    'jabatan' => $karyawan->jabatan ?? '-',
                    'is_resepsionis' => strtolower($karyawan->jabatan ?? '') === 'resepsionis',
                ];
            });

        return response()->json(['data' => $karyawans]);    
    }

    public function getKtpSignedUrl($tamuId)
    {
        try {
            $tamu = Tamu::findOrFail($tamuId);

            if (!$tamu->ktp_public_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'KTP tidak ditemukan'
                ], 404);
            }

            $cloudName = config('cloudinary.cloud_name');
            $apiKey = config('cloudinary.api_key');
            $apiSecret = config('cloudinary.api_secret');
            $publicId = $tamu->ktp_public_id;

            $timestamp = time() + 3600; 

            $toSign = "public_id={$publicId}&timestamp={$timestamp}{$apiSecret}";
            $signature = hash('sha1', $toSign);

            $signedUrl = sprintf(
                'https://res.cloudinary.com/%s/image/private/s--%s--/v1/%s?api_key=%s&timestamp=%s&signature=%s',
                $cloudName,
                substr($signature, 0, 8),
                $publicId,
                $apiKey,
                $timestamp,
                $signature
            );

            Log::info('Generated KTP signed URL', [
                'tamu_id' => $tamuId,
                'public_id' => $publicId,
                'url_preview' => substr($signedUrl, 0, 100) . '...'
            ]);

            return response()->json([
                'success' => true,
                'url' => $signedUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating signed URL for KTP', [
                'tamu_id' => $tamuId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat KTP: ' . $e->getMessage()
            ], 500);
        }
    }

    public function streamKtp($token)
    {
        try {
            $tamu = Tamu::where('ktp_access_token', $token)->firstOrFail();

            if (!$tamu->ktp_public_id) {
                abort(404, 'KTP tidak ditemukan');
            }

            $publicId = $tamu->ktp_public_id;
            
            // Generate cache filename from public_id (sanitize for filesystem)
            $cacheFilename = 'ktp-cache/' . md5($publicId) . '.jpg';
            
            // Check if cached version exists
            if (Storage::disk('local')->exists($cacheFilename)) {
                Log::info('Serving KTP from cache', [
                    'tamu_id' => $tamu->id_tamu,
                    'cache_file' => $cacheFilename
                ]);
                
                $imageContent = Storage::disk('local')->get($cacheFilename);
                
                return response($imageContent)
                    ->header('Content-Type', 'image/jpeg')
                    ->header('Cache-Control', 'private, max-age=600')
                    ->header('X-Content-Type-Options', 'nosniff');
            }

            // Not cached, download from Cloudinary
            $cloudName = config('cloudinary.cloud_name');

            Log::info('Downloading KTP from Cloudinary to cache', [
                'tamu_id' => $tamu->id_tamu,
                'public_id' => $publicId,
                'access_via' => 'token'
            ]);

            $imageUrl = sprintf(
                'https://res.cloudinary.com/%s/image/upload/%s',
                $cloudName,
                $publicId
            );

            $ch = curl_init($imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

            $imageContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode !== 200 || !$imageContent) {
                Log::error('Failed to download KTP', [
                    'http_code' => $httpCode,
                    'curl_error' => $error,
                    'tamu_id' => $tamu->id_tamu,
                    'public_id' => $publicId,
                    'url' => $imageUrl
                ]);

                $errorMsg = $httpCode === 401 ? 'KTP tidak dapat diakses' :
                    ($httpCode === 404 ? 'KTP tidak ditemukan' :
                        'Gagal memuat KTP - pastikan terhubung ke jaringan yang dapat mengakses Cloudinary');
                abort(500, $errorMsg);
            }

            // Save to cache for future requests
            Storage::disk('local')->put($cacheFilename, $imageContent);

            Log::info('Successfully downloaded and cached KTP', [
                'tamu_id' => $tamu->id_tamu,
                'size' => strlen($imageContent),
                'cache_file' => $cacheFilename
            ]);

            return response($imageContent)
                ->header('Content-Type', 'image/jpeg')
                ->header('Cache-Control', 'private, max-age=600')
                ->header('X-Content-Type-Options', 'nosniff');

        } catch (\Exception $e) {
            Log::error('Error streaming KTP', [
                'token' => $token,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            abort(500, 'Gagal memuat KTP');
        }
    }

    public function streamDokumentasi($token)
    {
        try {
            $dokumentasi = Dokumentasi::where('access_token', $token)->firstOrFail();

            if (!$dokumentasi->dokumentasi_public_id) {
                abort(404, 'Dokumentasi tidak ditemukan');
            }

            $publicId = $dokumentasi->dokumentasi_public_id;
            
            // Generate cache filename from public_id (sanitize for filesystem)
            $cacheFilename = 'dokumentasi-cache/' . md5($publicId) . '.jpg';
            
            // Check if cached version exists
            if (Storage::disk('local')->exists($cacheFilename)) {
                Log::info('Serving dokumentasi from cache', [
                    'dokumentasi_id' => $dokumentasi->id_dokumentasi,
                    'cache_file' => $cacheFilename
                ]);
                
                $imageContent = Storage::disk('local')->get($cacheFilename);
                
                return response($imageContent)
                    ->header('Content-Type', 'image/jpeg')
                    ->header('Cache-Control', 'private, max-age=3600')
                    ->header('X-Content-Type-Options', 'nosniff');
            }

            // Not cached, download from Cloudinary
            $cloudName = config('cloudinary.cloud_name');

            Log::info('Downloading dokumentasi from Cloudinary to cache', [
                'dokumentasi_id' => $dokumentasi->id_dokumentasi,
                'public_id' => $publicId,
                'access_via' => 'token'
            ]);

            $imageUrl = sprintf(
                'https://res.cloudinary.com/%s/image/upload/%s',
                $cloudName,
                $publicId
            );

            $ch = curl_init($imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

            $imageContent = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode !== 200 || !$imageContent) {
                Log::error('Failed to download dokumentasi', [
                    'http_code' => $httpCode,
                    'curl_error' => $error,
                    'dokumentasi_id' => $dokumentasi->id_dokumentasi,
                    'public_id' => $publicId,
                    'url' => $imageUrl
                ]);

                $errorMsg = $httpCode === 401 ? 'Dokumentasi tidak dapat diakses' :
                    ($httpCode === 404 ? 'Dokumentasi tidak ditemukan' :
                        'Gagal memuat dokumentasi - pastikan terhubung ke jaringan yang dapat mengakses Cloudinary');
                abort(500, $errorMsg);
            }

            // Save to cache for future requests
            Storage::disk('local')->put($cacheFilename, $imageContent);

            Log::info('Successfully downloaded and cached dokumentasi', [
                'dokumentasi_id' => $dokumentasi->id_dokumentasi,
                'size' => strlen($imageContent),
                'content_type' => $contentType,
                'cache_file' => $cacheFilename
            ]);

            return response($imageContent)
                ->header('Content-Type', $contentType ?: 'image/jpeg')
                ->header('Cache-Control', 'private, max-age=3600')
                ->header('X-Content-Type-Options', 'nosniff');

        } catch (\Exception $e) {
            Log::error('Error streaming dokumentasi', [
                'token' => $token,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            abort(500, 'Gagal memuat dokumentasi');
        }
    }

    private function sendNotificationToTamu(Kunjungan $kunjungan, string $status)
    {
        try {
            $tamu = $kunjungan->tamu;

            $karyawan = $kunjungan->karyawan()->first();

            if (!$karyawan) {
                Log::warning('No employee associated with kunjungan ID: ' . $kunjungan->id_kunjungan);
                return;
            }

            if (!$tamu->email_tamu) {
                Log::warning('Guest has no email, ID: ' . $tamu->id_tamu);
                return;
            }

            Log::info('Sending notification to guest: ' . $tamu->email_tamu);

            Mail::to($tamu->email_tamu)->send(
                new KunjunganNotification($tamu, $karyawan, $kunjungan, $status)
            );

            Log::info('Email notification successfully sent to: ' . $tamu->nama_tamu);

        } catch (\Exception $e) {
            Log::error('Failed to send email to guest: ' . $e->getMessage(), [
                'kunjungan_id' => $kunjungan->id_kunjungan,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendNotulensiRequestToKaryawan(Kunjungan $kunjungan)
    {
        try {
            // Load relationships
            $kunjungan->load(['tamu', 'karyawan']);

            // Get token for notulensi form
            $token = $kunjungan->token_approval;

            if (!$token) {
                Log::error('No token_approval found for kunjungan ID: ' . $kunjungan->id_kunjungan);
                return;
            }

            // Send email to each karyawan involved in the visit
            foreach ($kunjungan->karyawan as $karyawan) {
                if (!$karyawan->email_karyawan) {
                    Log::warning('Karyawan has no email, ID: ' . $karyawan->id_karyawan);
                    continue;
                }

                Log::info('Sending notulensi request to karyawan: ' . $karyawan->email_karyawan, [
                    'karyawan_id' => $karyawan->id_karyawan,
                    'kunjungan_id' => $kunjungan->id_kunjungan,
                ]);

                Mail::to($karyawan->email_karyawan)->send(
                    new NotulensiRequest($karyawan, $kunjungan, $token)
                );

                Log::info('Notulensi request email sent to: ' . $karyawan->nama_karyawan);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send notulensi request emails: ' . $e->getMessage(), [
                'kunjungan_id' => $kunjungan->id_kunjungan,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
