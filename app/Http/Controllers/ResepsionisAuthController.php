<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Resepsionis;

class ResepsionisAuthController extends Controller
{
    /**
     * Menampilkan halaman form login resepsionis
     */
    public function showLoginForm(Request $request)
    {
        // Logout dulu jika masih ada session login sebelumnya
        if (Auth::guard('resepsionis')->check()) {
            Auth::guard('resepsionis')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('resepsionis.login');
    }

    /**
     * Proses login resepsionis
     */
    public function login(Request $request)
    {
        // Validasi input email dan password
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        // Cari resepsionis berdasarkan email
        $resepsionis = Resepsionis::where('email_resepsionis', $request->email)->first();

        // Cek apakah email ditemukan dan password cocok
        if ($resepsionis && Hash::check($request->password, $resepsionis->password_resepsionis)) {
            // Login menggunakan guard resepsionis
            Auth::guard('resepsionis')->login($resepsionis, $request->filled('remember'));

            // Regenerate session untuk keamanan (mencegah session fixation)
            $request->session()->regenerate();

            // Redirect ke dashboard dengan pesan sukses
            return redirect()->intended(route('resepsionis.dashboard'))
                ->with('success', 'Selamat datang, ' . $resepsionis->nama_resepsionis);
        }

        // Jika gagal, kembali ke halaman login dengan error
        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->onlyInput('email');
    }

    /**
     * Proses logout resepsionis
     */
    public function logout(Request $request)
    {
        // Logout dari guard resepsionis
        Auth::guard('resepsionis')->logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect ke halaman login dengan pesan sukses
        return redirect()->route('resepsionis.login')
            ->with('success', 'Anda berhasil logout');
    }
}
