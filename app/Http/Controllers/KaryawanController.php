<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Resepsionis;
use App\Mail\ResepsionisInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class KaryawanController extends Controller
{
    public function createKaryawan()
    {
        return view('resepsionis.create-karyawan');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_karyawan' => 'required|string|max:255',
            'email_karyawan' => 'required|email|max:255|unique:karyawan,email_karyawan',
            'departemen' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
        ], [
            'nama_karyawan.required' => 'Nama karyawan wajib diisi',
            'email_karyawan.required' => 'Email karyawan wajib diisi',
            'email_karyawan.email' => 'Format email tidak valid',
            'email_karyawan.unique' => 'Email sudah terdaftar',
            'departemen.required' => 'Departemen wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
        ]);

        try {
            DB::beginTransaction();

            $karyawan = Karyawan::create($validated);

            // Check if jabatan is "resepsionis"
            if (strtolower($validated['jabatan']) === 'resepsionis') {
                // Generate token for account creation
                $token = Str::random(64);
                $expiredAt = now()->addHours(48); // Token valid for 48 hours

                // Create resepsionis record without password (will be set via email link)
                Resepsionis::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'nama_resepsionis' => $karyawan->nama_karyawan,
                    'email_resepsionis' => $karyawan->email_karyawan,
                    'token_setup' => $token,
                    'token_setup_expired_at' => $expiredAt,
                ]);

                // Send invitation email
                Mail::to($karyawan->email_karyawan)->send(
                    new ResepsionisInvitation($karyawan, $token)
                );

                DB::commit();

                return redirect()->route('resepsionis.karyawan')
                    ->with('success', 'Karyawan berhasil ditambahkan dan undangan pembuatan akun telah dikirim ke email');
            }

            DB::commit();

            return redirect()->route('resepsionis.karyawan')
                ->with('success', 'Karyawan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menambahkan karyawan: ' . $e->getMessage()]);
        }
    }

    public function searchDepartemen(Request $request)
    {
        $keyword = $request->input('q', '');

        $query = Karyawan::select('departemen')
            ->whereNotNull('departemen')
            ->distinct()
            ->orderBy('departemen', 'asc');

        if (!empty($keyword)) {
            $query->where('departemen', 'ILIKE', '%' . $keyword . '%');
        }

        $departemens = $query->pluck('departemen');

        return response()->json($departemens);
    }

    public function searchJabatan(Request $request)
    {
        $keyword = $request->input('q', '');

        $query = Karyawan::select('jabatan')
            ->whereNotNull('jabatan')
            ->distinct()
            ->orderBy('jabatan', 'asc');

        if (!empty($keyword)) {
            $query->where('jabatan', 'ILIKE', '%' . $keyword . '%');
        }

        $jabatans = $query->pluck('jabatan');

        return response()->json($jabatans);
    }

    public function toggleStatus($id)
    {
        try {
            $karyawan = Karyawan::findOrFail($id);

            // Toggle status
            $newStatus = $karyawan->status === 'aktif' ? 'nonaktif' : 'aktif';
            $karyawan->update(['status' => $newStatus]);

            $message = $newStatus === 'nonaktif'
                ? 'Karyawan berhasil dinonaktifkan'
                : 'Karyawan berhasil diaktifkan';

            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status karyawan: ' . $e->getMessage()
            ], 500);
        }
    }
}
