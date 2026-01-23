<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\KunjunganNotification;
use App\Mail\NotulensiRequest;
use Illuminate\Support\Facades\Log;

class KunjunganConfirmController extends Controller
{

    public function confirm(Request $request, $token)
    {
        $action = $request->get('action'); 
        
        Log::info('Menampilkan halaman konfirmasi', [
            'token' => $token,
            'action' => $action,
        ]);

        $kunjungan = Kunjungan::where('token_approval', $token)->first();

        if (!$kunjungan) {
            return view('kunjungan.error', [
                'message' => 'Link konfirmasi tidak valid atau sudah tidak berlaku.'
            ]);
        }

        if ($kunjungan->expired_at && now()->isAfter($kunjungan->expired_at)) {
            return view('kunjungan.error', [
                'message' => 'Link konfirmasi sudah kadaluarsa (berlaku 24 jam).'
            ]);
        }

        if ($kunjungan->status !== 'pending') {
            return view('kunjungan.error', [
                'message' => 'Kunjungan ini sudah dikonfirmasi sebelumnya dengan status: ' . $kunjungan->status_label
            ]);
        }

        $kunjungan->load('tamu');

        return view('kunjungan.confirm', [
            'kunjungan' => $kunjungan,
            'action' => $action,
        ]);
    }

    public function process(Request $request, $token)
    {
        $action = $request->input('action'); 
        
        Log::info('Proses konfirmasi kunjungan', [
            'token' => $token,
            'action' => $action,
        ]);

        $kunjungan = Kunjungan::where('token_approval', $token)->first();

        if (!$kunjungan) {
            return view('kunjungan.error', [
                'message' => 'Link konfirmasi tidak valid atau sudah tidak berlaku.'
            ]);
        }

        if ($kunjungan->expired_at && now()->isAfter($kunjungan->expired_at)) {
            return view('kunjungan.error', [
                'message' => 'Link konfirmasi sudah kadaluarsa (berlaku 24 jam).'
            ]);
        }

        if ($kunjungan->status !== 'pending') {
            return view('kunjungan.error', [
                'message' => 'Kunjungan ini sudah dikonfirmasi sebelumnya dengan status: ' . $kunjungan->status_label
            ]);
        }

        if ($action === 'terima') {
            $kunjungan->status = 'approved'; 
            $kunjungan->save();
            
            Log::info('Kunjungan diterima (approved), ID: ' . $kunjungan->id_kunjungan);

            $this->sendNotificationToTamu($kunjungan, 'diterima');

            $this->sendNotulensiRequestToKaryawan($kunjungan, $token);

            return view('kunjungan.success', [
                'type' => 'terima',
                'kunjungan' => $kunjungan->load('tamu'),
                'message' => 'Anda telah menerima kunjungan ini. Tamu akan segera diberitahu via email.'
            ]);

        } elseif ($action === 'tolak') {
            $request->validate([
                'alasan_penolakan' => 'required|string|max:500',
            ], [
                'alasan_penolakan.required' => 'Alasan penolakan harus diisi.',
                'alasan_penolakan.max' => 'Alasan penolakan maksimal 500 karakter.',
            ]);

            $kunjungan->status = 'canceled'; 
            $kunjungan->alasan_batal = $request->input('alasan_penolakan');
            $kunjungan->save();
            
            Log::info('Kunjungan ditolak (canceled), ID: ' . $kunjungan->id_kunjungan . ', Alasan: ' . $kunjungan->alasan_batal);

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

    private function sendNotificationToTamu(Kunjungan $kunjungan, string $status)
    {
        try {
            $tamu = $kunjungan->tamu;
            
            $karyawan = $kunjungan->karyawan()->first();
            
            if (!$karyawan) {
                Log::warning('Tidak ada karyawan yang terkait dengan kunjungan ID: ' . $kunjungan->id_kunjungan);
                return;
            }
            
            if (!$tamu->email_tamu) {
                Log::warning('Tamu tidak memiliki email, ID: ' . $tamu->id_tamu);
                return;
            }
            
            Log::info('Mengirim notifikasi ke tamu: ' . $tamu->email_tamu);
            
            Mail::to($tamu->email_tamu)->send(
                new KunjunganNotification($tamu, $karyawan, $kunjungan, $status)
            );
            
            Log::info('Email notifikasi berhasil dikirim ke: ' . $tamu->nama_tamu);
            
        } catch (\Exception $e) {
            Log::error('Gagal kirim email ke tamu: ' . $e->getMessage(), [
                'kunjungan_id' => $kunjungan->id_kunjungan,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendNotulensiRequestToKaryawan(Kunjungan $kunjungan, string $token)
    {
        try {
            $karyawanList = $kunjungan->karyawan;
            
            if ($karyawanList->isEmpty()) {
                Log::warning('Tidak ada karyawan untuk kirim notulensi request, ID: ' . $kunjungan->id_kunjungan);
                return;
            }
            
            foreach ($karyawanList as $karyawan) {
                if (!$karyawan->email_karyawan) {
                    Log::warning('Karyawan tidak memiliki email, ID: ' . $karyawan->id_karyawan . ', Nama: ' . $karyawan->nama_karyawan);
                    continue;
                }
                
                Log::info('Mengirim email notulensi request ke karyawan: ' . $karyawan->email_karyawan);
                
                Mail::to($karyawan->email_karyawan)->send(
                    new NotulensiRequest($karyawan, $kunjungan, $token)
                );
                
                Log::info('Email notulensi request berhasil dikirim ke: ' . $karyawan->nama_karyawan);
            }
            
        } catch (\Exception $e) {
            Log::error('Gagal kirim email notulensi request: ' . $e->getMessage(), [
                'kunjungan_id' => $kunjungan->id_kunjungan,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
