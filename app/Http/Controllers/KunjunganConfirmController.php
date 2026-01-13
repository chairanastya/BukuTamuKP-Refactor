<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\KunjunganNotification;

class KunjunganConfirmController extends Controller
{
    /**
     * Tampilkan halaman konfirmasi
     */
    public function confirm(Request $request, $token)
    {
        $action = $request->get('action'); // 'terima' atau 'tolak'
        
        \Log::info('Menampilkan halaman konfirmasi', [
            'token' => $token,
            'action' => $action,
        ]);

        // Find kunjungan by token
        $kunjungan = Kunjungan::where('token_approval', $token)->first();

        if (!$kunjungan) {
            return view('kunjungan.error', [
                'message' => 'Link konfirmasi tidak valid atau sudah tidak berlaku.'
            ]);
        }

        // Check if token expired
        if ($kunjungan->expired_at && now()->isAfter($kunjungan->expired_at)) {
            return view('kunjungan.error', [
                'message' => 'Link konfirmasi sudah kadaluarsa (berlaku 24 jam).'
            ]);
        }

        // Check if already confirmed
        if ($kunjungan->status !== 'pending') {
            return view('kunjungan.error', [
                'message' => 'Kunjungan ini sudah dikonfirmasi sebelumnya dengan status: ' . $kunjungan->status_label
            ]);
        }

        // Load relasi tamu
        $kunjungan->load('tamu');

        // Tampilkan halaman konfirmasi
        return view('kunjungan.confirm', [
            'kunjungan' => $kunjungan,
            'action' => $action,
        ]);
    }

    /**
     * Proses konfirmasi setelah user klik tombol di halaman konfirmasi
     */
    public function process(Request $request, $token)
    {
        $action = $request->input('action'); // 'terima' atau 'tolak'
        
        \Log::info('Proses konfirmasi kunjungan', [
            'token' => $token,
            'action' => $action,
        ]);

        // Find kunjungan by token
        $kunjungan = Kunjungan::where('token_approval', $token)->first();

        if (!$kunjungan) {
            return view('kunjungan.error', [
                'message' => 'Link konfirmasi tidak valid atau sudah tidak berlaku.'
            ]);
        }

        // Check if token expired
        if ($kunjungan->expired_at && now()->isAfter($kunjungan->expired_at)) {
            return view('kunjungan.error', [
                'message' => 'Link konfirmasi sudah kadaluarsa (berlaku 24 jam).'
            ]);
        }

        // Check if already confirmed
        if ($kunjungan->status !== 'pending') {
            return view('kunjungan.error', [
                'message' => 'Kunjungan ini sudah dikonfirmasi sebelumnya dengan status: ' . $kunjungan->status_label
            ]);
        }

        // Process confirmation
        if ($action === 'terima') {
            $kunjungan->status = 'approved'; // approved = diterima
            $kunjungan->save();
            
            \Log::info('Kunjungan diterima (approved), ID: ' . $kunjungan->id_kunjungan);

            // Kirim email notifikasi ke tamu
            $this->sendNotificationToTamu($kunjungan, 'diterima');

            return view('kunjungan.success', [
                'type' => 'terima',
                'kunjungan' => $kunjungan->load('tamu'),
                'message' => 'Anda telah menerima kunjungan ini. Tamu akan segera diberitahu via email.'
            ]);

        } elseif ($action === 'tolak') {
            $kunjungan->status = 'canceled'; // canceled = ditolak
            $kunjungan->save();
            
            \Log::info('Kunjungan ditolak (canceled), ID: ' . $kunjungan->id_kunjungan);

            // Kirim email notifikasi ke tamu
            $this->sendNotificationToTamu($kunjungan, 'ditolak');

            return view('kunjungan.success', [
                'type' => 'tolak',
                'kunjungan' => $kunjungan->load('tamu'),
                'message' => 'Kunjungan telah ditolak. Tamu akan diberitahu via email untuk menjadwalkan ulang.'
            ]);

        } else {
            return view('kunjungan.error', [
                'message' => 'Aksi tidak valid.'
            ]);
        }
    }

    /**
     * Kirim notifikasi email ke tamu
     */
    private function sendNotificationToTamu(Kunjungan $kunjungan, string $status)
    {
        try {
            $tamu = $kunjungan->tamu;
            
            // Ambil karyawan pertama yang dituju (jika ada multiple, ambil yang pertama)
            $karyawan = $kunjungan->karyawan()->first();
            
            if (!$karyawan) {
                \Log::warning('Tidak ada karyawan yang terkait dengan kunjungan ID: ' . $kunjungan->id_kunjungan);
                return;
            }
            
            if (!$tamu->email_tamu) {
                \Log::warning('Tamu tidak memiliki email, ID: ' . $tamu->id_tamu);
                return;
            }
            
            \Log::info('Mengirim notifikasi ke tamu: ' . $tamu->email_tamu);
            
            Mail::to($tamu->email_tamu)->send(
                new KunjunganNotification($tamu, $karyawan, $kunjungan, $status)
            );
            
            \Log::info('Email notifikasi berhasil dikirim ke: ' . $tamu->nama_tamu);
            
        } catch (\Exception $e) {
            \Log::error('Gagal kirim email ke tamu: ' . $e->getMessage(), [
                'kunjungan_id' => $kunjungan->id_kunjungan,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
