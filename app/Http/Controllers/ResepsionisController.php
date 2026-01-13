<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepsionisController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();
        
        // Get statistics for today
        $stats = [
            'total' => Kunjungan::whereDate('tanggal_kunjungan', $today)->count(),
            'pending' => Kunjungan::whereDate('tanggal_kunjungan', $today)->where('status', 'pending')->count(),
            'done' => Kunjungan::whereDate('tanggal_kunjungan', $today)->where('status', 'done')->count(),
            'canceled' => Kunjungan::whereDate('tanggal_kunjungan', $today)->where('status', 'canceled')->count(),
        ];

        return view('resepsionis.dashboard', compact('stats'));
    }

    public function getKunjunganData(Request $request)
    {
        $query = Kunjungan::with(['tamu', 'karyawan'])
            ->whereDate('tanggal_kunjungan', now()->toDateString())
            ->orderBy('tanggal_kunjungan', 'desc')
            ->orderBy('jam_mulai', 'desc');

        $kunjungans = $query->get()->map(function ($kunjungan) {
            // Build Cloudinary URL if public_id exists
            $ktpUrl = null;
            if ($kunjungan->tamu && $kunjungan->tamu->ktp_public_id) {
                $cloudName = config('cloudinary.cloud_name');
                $ktpUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$kunjungan->tamu->ktp_public_id}";
            }

            return [
                'id_kunjungan' => $kunjungan->id_kunjungan,
                'tanggal' => $kunjungan->tanggal_kunjungan->format('d/m/Y'),
                'jam' => substr($kunjungan->jam_mulai, 0, 5) . ' - ' . substr($kunjungan->jam_selesai ?? '00:00', 0, 5),
                'nama_tamu' => $kunjungan->tamu->nama_tamu ?? '-',
                'email_tamu' => $kunjungan->tamu->email_tamu ?? '-',
                'ktp_url' => $ktpUrl,
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

        $kunjungan->update(['status' => 'accepted']);

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

        return response()->json(['success' => true, 'message' => 'Kunjungan berhasil ditolak']);
    }

    public function riwayat()
    {
        return view('resepsionis.riwayat');
    }

    public function daftarKaryawan()
    {
        $karyawans = Karyawan::orderBy('nama_karyawan')->get();
        return view('resepsionis.karyawan', compact('karyawans'));
    }
}
