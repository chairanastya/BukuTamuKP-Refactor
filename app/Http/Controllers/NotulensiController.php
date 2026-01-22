<?php

namespace App\Http\Controllers;

use App\Models\Notulensi;
use App\Models\Kunjungan;
use App\Models\Dokumentasi;
use App\Mail\NotulensiAvailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class NotulensiController extends Controller
{

    public function create($token)
    {
        $kunjungan = Kunjungan::where('token_approval', $token)->first();

        if (!$kunjungan) {
            return view('notulensi.error', [
                'message' => 'Link tidak valid atau sudah tidak berlaku.'
            ]);
        }

        if ($kunjungan->status !== 'approved') {
            return view('notulensi.error', [
                'message' => 'Kunjungan ini belum disetujui atau sudah dibatalkan.'
            ]);
        }

        $existingNotulensi = Notulensi::where('id_kunjungan', $kunjungan->id_kunjungan)->first();
        if ($existingNotulensi) {
            return view('notulensi.error', [
                'message' => 'Notulensi untuk kunjungan ini sudah pernah diisi.'
            ]);
        }

        $kunjungan->load(['tamu', 'karyawan']);

        return view('notulensi.create', [
            'kunjungan' => $kunjungan,
            'token' => $token,
        ]);
    }

    public function store(Request $request, $token)
    {
        $request->validate([
            'jam_selesai' => 'required|date_format:H:i',
            'anggota_rapat' => 'nullable|string|max:1000',
            'isi_notulensi' => 'required|string|min:50',
            'dokumentasi.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB per file
        ], [
            'jam_selesai.required' => 'Jam selesai harus diisi.',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid.',
            'isi_notulensi.required' => 'Isi notulensi harus diisi.',
            'isi_notulensi.min' => 'Isi notulensi minimal 50 karakter.',
            'dokumentasi.*.image' => 'File harus berupa gambar.',
            'dokumentasi.*.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'dokumentasi.*.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $kunjungan = Kunjungan::where('token_approval', $token)->first();

        if (!$kunjungan || $kunjungan->status !== 'approved') {
            return back()->with('error', 'Kunjungan tidak valid.');
        }

        $existingNotulensi = Notulensi::where('id_kunjungan', $kunjungan->id_kunjungan)->first();
        if ($existingNotulensi) {
            return back()->with('error', 'Notulensi sudah pernah diisi.');
        }

        $kunjungan->jam_selesai = $request->input('jam_selesai');
        $kunjungan->save();

        $tokenTamu = Str::random(64);

        $notulensi = Notulensi::create([
            'id_kunjungan' => $kunjungan->id_kunjungan,
            'anggota_rapat' => $request->input('anggota_rapat'),
            'isi_notulensi' => $request->input('isi_notulensi'),
            'token_karyawan' => $token,
            'token_tamu' => $tokenTamu,
            'expired_at' => now()->addDays(30), 
        ]);

        if ($request->hasFile('dokumentasi')) {
            $this->uploadDokumentasi($request->file('dokumentasi'), $kunjungan->id_kunjungan);
        }

        $kunjungan->status = 'done';
        $kunjungan->save();

        $this->sendNotulensiToParticipants($kunjungan, $tokenTamu);

        return view('notulensi.success', [
            'message' => 'Notulensi berhasil disimpan. Tamu dan karyawan akan diberitahu via email.',
            'kunjungan' => $kunjungan->load('tamu'),
        ]);
    }

    public function view($token)
    {
        $notulensi = Notulensi::where('token_tamu', $token)->first();

        if (!$notulensi) {
            return view('notulensi.error', [
                'message' => 'Link tidak valid atau sudah tidak berlaku.'
            ]);
        }

        if ($notulensi->expired_at && now()->isAfter($notulensi->expired_at)) {
            return view('notulensi.error', [
                'message' => 'Link sudah kadaluarsa.'
            ]);
        }

        $notulensi->load(['kunjungan.tamu', 'kunjungan.karyawan', 'kunjungan.dokumentasi']);

        return view('notulensi.view', [
            'notulensi' => $notulensi,
        ]);
    }

    private function sendNotulensiToParticipants(Kunjungan $kunjungan, string $token)
    {
        // Kirim email ke tamu
        try {
            $tamu = $kunjungan->tamu;

            if ($tamu->email_tamu) {
                Log::info('Mengirim email notulensi ke tamu: ' . $tamu->email_tamu);

                Mail::to($tamu->email_tamu)->send(
                    new NotulensiAvailable($tamu->nama_tamu, $kunjungan, $token)
                );

                Log::info('Email notulensi berhasil dikirim ke tamu: ' . $tamu->nama_tamu);
            } else {
                Log::warning('Tamu tidak memiliki email untuk notulensi, ID: ' . $tamu->id_tamu);
            }
        } catch (\Exception $e) {
            Log::error('Gagal kirim email notulensi ke tamu: ' . $e->getMessage(), [
                'kunjungan_id' => $kunjungan->id_kunjungan,
                'error' => $e->getMessage(),
            ]);
        }

        // Kirim email ke semua karyawan yang terlibat
        $karyawanList = $kunjungan->karyawan;
        
        foreach ($karyawanList as $karyawan) {
            try {
                if ($karyawan->email_karyawan) {
                    Log::info('Mengirim email notulensi ke karyawan: ' . $karyawan->email_karyawan);

                    Mail::to($karyawan->email_karyawan)->send(
                        new NotulensiAvailable($karyawan->nama_karyawan, $kunjungan, $token)
                    );

                    Log::info('Email notulensi berhasil dikirim ke karyawan: ' . $karyawan->nama_karyawan);
                } else {
                    Log::warning('Karyawan tidak memiliki email untuk notulensi, ID: ' . $karyawan->id_karyawan);
                }
            } catch (\Exception $e) {
                Log::error('Gagal kirim email notulensi ke karyawan: ' . $e->getMessage(), [
                    'kunjungan_id' => $kunjungan->id_kunjungan,
                    'karyawan_id' => $karyawan->id_karyawan,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    private function uploadDokumentasi(array $files, int $idKunjungan)
    {
        try {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('cloudinary.cloud_name'),
                    'api_key' => config('cloudinary.api_key'),
                    'api_secret' => config('cloudinary.api_secret'),
                ],
            ]);

            foreach ($files as $file) {
                $uploadResult = $cloudinary->uploadApi()->upload(
                    $file->getRealPath(),
                    [
                        'folder' => 'buku_tamu/dokumentasi',
                        'resource_type' => 'image',
                    ]
                );

                Dokumentasi::create([
                    'id_kunjungan' => $idKunjungan,
                    'dokumentasi_public_id' => $uploadResult['public_id'],
                    'dokumentasi_url' => $uploadResult['secure_url'],
                    'uploaded_at' => now(),
                ]);
            }

            Log::info('Dokumentasi berhasil diupload untuk kunjungan: ' . $idKunjungan);

        } catch (\Exception $e) {
            Log::error('Gagal upload dokumentasi: ' . $e->getMessage(), [
                'kunjungan_id' => $idKunjungan,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

