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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\KunjunganNotification;
use App\Mail\NotulensiRequest;
use App\Helpers\BadgeHelper;

class ResepsionisController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();

        $todayStats = Kunjungan::whereDate('tanggal_kunjungan', $today)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending")
            ->selectRaw("COUNT(CASE WHEN status = 'done' THEN 1 END) as done")
            ->selectRaw("COUNT(CASE WHEN status = 'canceled' THEN 1 END) as canceled")
            ->first();

        $stats = [
            'total' => $todayStats->total ?? 0,
            'pending' => $todayStats->pending ?? 0,
            'done' => $todayStats->done ?? 0,
            'canceled' => $todayStats->canceled ?? 0,
        ];

        $allTime = Kunjungan::selectRaw('COUNT(*) as total')
            ->selectRaw("COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending")
            ->selectRaw("COUNT(CASE WHEN status = 'done' THEN 1 END) as done")
            ->selectRaw("COUNT(CASE WHEN status = 'canceled' THEN 1 END) as canceled")
            ->first();

        $allTimeStats = [
            'total' => $allTime->total ?? 0,
            'pending' => $allTime->pending ?? 0,
            'done' => $allTime->done ?? 0,
            'canceled' => $allTime->canceled ?? 0,
        ];

        return view('resepsionis.dashboard', compact('stats', 'allTimeStats'));
    }

    public function getKunjunganData(Request $request)
    {
        $query = Kunjungan::with([
            'tamu:id_tamu,nama_tamu,email_tamu,instansi_tamu,ktp_public_id,ktp_access_token',
            'karyawan:id_karyawan,nama_karyawan,jabatan,departemen'
        ])
            ->select('id_kunjungan', 'id_tamu', 'tujuan_kunjungan', 'tanggal_kunjungan', 'jam_mulai', 'jam_selesai', 'status', 'alasan_batal')
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
                'status_badge' => BadgeHelper::getStatusBadge($kunjungan->status),
                'alasan_batal' => $kunjungan->alasan_batal,
            ];
        });

        return response()->json(['data' => $kunjungans]);
    }

    public function getRiwayatData(Request $request)
    {
        $perPage = $request->input('per_page', 100);
        $page = $request->input('page', 1);

        $query = Kunjungan::with([
            'tamu:id_tamu,nama_tamu,email_tamu,instansi_tamu,ktp_public_id,ktp_access_token',
            'karyawan:id_karyawan,nama_karyawan,jabatan,departemen'
        ])
            ->select('id_kunjungan', 'id_tamu', 'tujuan_kunjungan', 'tanggal_kunjungan', 'jam_mulai', 'jam_selesai', 'status', 'alasan_batal')
            ->orderBy('tanggal_kunjungan', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->limit($perPage)
            ->offset(($page - 1) * $perPage);

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
                'status_badge' => BadgeHelper::getStatusBadge($kunjungan->status), // Helper yang mereplikasi logic badge.blade.php tanpa rendering
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

        $this->sendNotificationToTamu($kunjungan, 'diterima');

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
            'aktif' => Karyawan::where('status', 'aktif')->count(),
            'nonaktif' => Karyawan::where('status', 'nonaktif')->count(),
            'departemen' => Karyawan::distinct('departemen')->count('departemen'),
        ];

        return view('resepsionis.karyawan', compact('stats'));
    }

    public function getKaryawanData(Request $request)
    {
        $karyawans = Karyawan::orderBy('created_at', 'desc')
            ->get()
            ->map(function ($karyawan) {
                return [
                    'id_karyawan' => $karyawan->id_karyawan,
                    'nama_karyawan' => $karyawan->nama_karyawan,
                    'email_karyawan' => $karyawan->email_karyawan,
                    'departemen' => $karyawan->departemen ?? '-',
                    'jabatan' => $karyawan->jabatan ?? '-',
                    'status' => $karyawan->status,
                    'status_badge' => BadgeHelper::getStatusBadge($karyawan->status),
                    'is_resepsionis' => strtolower($karyawan->jabatan ?? '') === 'resepsionis',
                    'role_badge' => BadgeHelper::getStatusBadge(strtolower($karyawan->jabatan ?? '') === 'resepsionis' ? 'resepsionis' : 'karyawan'),
                    'created_at' => $karyawan->created_at?->format('Y-m-d H:i:s'),
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

            // Generate local storage URL
            $localUrl = Storage::disk('public')->url($tamu->ktp_public_id);

            Log::info('Generated KTP local URL', [
                'tamu_id' => $tamuId,
                'file_path' => $tamu->ktp_public_id,
                'url' => $localUrl
            ]);

            return response()->json([
                'success' => true,
                'url' => $localUrl
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
            // Validasi token dan pastikan tamu ada
            $tamu = Tamu::where('ktp_access_token', $token)->firstOrFail();

            // Authorization: Pastikan tamu ini memiliki kunjungan yang tercatat
            // Ini mencegah akses ke KTP tamu yang tidak pernah berkunjung/data invalid
            $hasKunjungan = $tamu->kunjungan()->exists();
            if (!$hasKunjungan) {
                Log::warning('Unauthorized KTP access attempt - no kunjungan record', [
                    'tamu_id' => $tamu->id_tamu,
                    'resepsionis_id' => auth('resepsionis')->id(),
                ]);
                abort(403, 'Tidak memiliki akses ke KTP ini');
            }

            if (!$tamu->ktp_public_id) {
                abort(404, 'KTP tidak ditemukan');
            }

            $filePath = $tamu->ktp_public_id;

            // Check if file exists in local storage
            if (!Storage::disk('public')->exists($filePath)) {
                Log::error('KTP file not found in storage', [
                    'tamu_id' => $tamu->id_tamu,
                    'file_path' => $filePath
                ]);
                abort(404, 'File KTP tidak ditemukan');
            }

            Log::info('Serving KTP from local storage', [
                'tamu_id' => $tamu->id_tamu,
                'file_path' => $filePath
            ]);

            $imageContent = Storage::disk('public')->get($filePath);

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
            // Validasi token dan load relasi kunjungan untuk authorization
            $dokumentasi = Dokumentasi::with('kunjungan')->where('access_token', $token)->firstOrFail();

            // Authorization: Pastikan dokumentasi terkait dengan kunjungan yang valid
            if (!$dokumentasi->kunjungan) {
                Log::warning('Unauthorized dokumentasi access attempt - no kunjungan', [
                    'dokumentasi_id' => $dokumentasi->id_dokumentasi,
                    'resepsionis_id' => auth('resepsionis')->id(),
                ]);
                abort(403, 'Tidak memiliki akses ke dokumentasi ini');
            }

            // Pastikan kunjungan dalam status yang valid (approved atau done)
            if (!in_array($dokumentasi->kunjungan->status, ['approved', 'done'])) {
                Log::warning('Unauthorized dokumentasi access - invalid kunjungan status', [
                    'dokumentasi_id' => $dokumentasi->id_dokumentasi,
                    'kunjungan_status' => $dokumentasi->kunjungan->status,
                    'resepsionis_id' => auth('resepsionis')->id(),
                ]);
                abort(403, 'Dokumentasi hanya dapat diakses untuk kunjungan yang sudah disetujui');
            }

            if (!$dokumentasi->dokumentasi_public_id) {
                abort(404, 'Dokumentasi tidak ditemukan');
            }

            $filePath = $dokumentasi->dokumentasi_public_id;

            // Check if file exists in local storage
            if (!Storage::disk('public')->exists($filePath)) {
                Log::error('Dokumentasi file not found in storage', [
                    'dokumentasi_id' => $dokumentasi->id_dokumentasi,
                    'file_path' => $filePath
                ]);
                abort(404, 'File dokumentasi tidak ditemukan');
            }

            Log::info('Serving dokumentasi from local storage', [
                'dokumentasi_id' => $dokumentasi->id_dokumentasi,
                'file_path' => $filePath
            ]);

            $imageContent = Storage::disk('public')->get($filePath);
            
            // Determine content type from file extension
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            $contentType = match(strtolower($extension)) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                default => 'image/jpeg'
            };

            return response($imageContent)
                ->header('Content-Type', $contentType)
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
            $kunjungan->load(['tamu', 'karyawan']);

            $token = $kunjungan->token_approval;

            if (!$token) {
                Log::error('No token_approval found for kunjungan ID: ' . $kunjungan->id_kunjungan);
                return;
            }

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

    public function getNotulensiToken($kunjunganId)
    {
        try {
            $notulensi = \App\Models\Notulensi::where('id_kunjungan', $kunjunganId)->first();

            if (!$notulensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notulensi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'token' => $notulensi->token_tamu
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get notulensi token: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data notulensi'
            ], 500);
        }
    }

    /**
     * Show the change password form.
     */
    public function editPassword()
    {
        return view('resepsionis.change-password');
    }

    /**
     * Verify current password via AJAX.
     */
    public function verifyPassword(Request $request)
    {
        $user = Auth::guard('resepsionis')->user();

        $isValid = Hash::check($request->current_password, $user->password_resepsionis);

        return response()->json([
            'valid' => $isValid
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi',
            'new_password.required' => 'Password baru wajib diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = Auth::guard('resepsionis')->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password_resepsionis)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai'
            ])->withInput();
        }

        // Check if new password is same as current
        if (Hash::check($request->new_password, $user->password_resepsionis)) {
            return back()->withErrors([
                'new_password' => 'Password baru tidak boleh sama dengan password saat ini'
            ])->withInput();
        }

        try {
            // Update password
            $user->password_resepsionis = Hash::make($request->new_password);
            $user->save();

            Log::info('Password changed successfully for resepsionis: ' . $user->email_resepsionis);

            return redirect()->route('resepsionis.password.edit')
                ->with('status', 'Password berhasil diubah!');
        } catch (\Exception $e) {
            Log::error('Failed to update password: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat mengubah password. Silakan coba lagi.'
            ])->withInput();
        }
    }
}
