<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendNotificationEmailJob;
use App\Jobs\SendNotulensiRequestJob;

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

            dispatch(new SendNotificationEmailJob($kunjungan->id_kunjungan, 'diterima'));
            dispatch(new SendNotulensiRequestJob($kunjungan->id_kunjungan, $token));

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

            dispatch(new SendNotificationEmailJob($kunjungan->id_kunjungan, 'ditolak'));

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
}
