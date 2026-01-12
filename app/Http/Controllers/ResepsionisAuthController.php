<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Resepsionis;

class ResepsionisAuthController extends Controller
{
    /**
     * Token expiration time in minutes
     */
    private const TOKEN_EXPIRATION_MINUTES = 60;

    /**
     * Password reset tokens table name
     */
    private const PASSWORD_RESET_TABLE = 'password_reset_tokens';
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

    /**
     * Menampilkan form forgot password
     */
    public function showForgotPasswordForm()
    {
        return view('resepsionis.forgot-password');
    }

    /**
     * Mengirim email reset password link
     */
    public function sendResetLinkEmail(Request $request)
    {
        $email = $this->validateEmail($request);

        $resepsionis = $this->findResepsionisOrFail($email);

        $token = $this->createPasswordResetToken($email);

        $this->sendPasswordResetEmail($email, $token);

        return back()->with('status', 'Link reset password telah dikirim ke email Anda! Silakan cek inbox atau folder spam.');
    }

    /**
     * Menampilkan form reset password
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('resepsionis.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Proses reset password
     */
    public function resetPassword(Request $request)
    {
        $this->validatePasswordReset($request);

        $passwordReset = $this->findValidPasswordReset($request->token);

        $resepsionis = $this->updateResepsionisPassword($passwordReset->email, $request->password);

        $this->deletePasswordResetToken($passwordReset->email);

        return redirect()->route('resepsionis.login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Validate email input
     */
    private function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        return $request->email;
    }

    /**
     * Validate password reset input
     */
    private function validatePasswordReset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'token.required' => 'Token reset password tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);
    }

    /**
     * Find resepsionis by email or fail
     */
    private function findResepsionisOrFail($email)
    {
        $resepsionis = Resepsionis::where('email_resepsionis', $email)->first();

        if (!$resepsionis) {
            abort(back()->withErrors([
                'email' => 'Email tidak ditemukan dalam sistem kami.',
            ])->onlyInput('email')->send());
        }

        return $resepsionis;
    }

    /**
     * Create password reset token
     */
    private function createPasswordResetToken($email)
    {
        $token = Str::random(64);

        DB::table(self::PASSWORD_RESET_TABLE)->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        return $token;
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail($email, $token)
    {
        try {
            Mail::send('emails.reset-password', ['token' => $token, 'email' => $email], function ($message) use ($email) {
                $message->to($email);
                $message->subject('Reset Password - Buku Tamu Digital');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send reset password email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            abort(back()->withErrors([
                'email' => 'Gagal mengirim email. Pastikan konfigurasi email sudah benar.'
            ])->onlyInput('email')->send());
        }
    }

    /**
     * Find valid password reset by token
     */
    private function findValidPasswordReset($token)
    {
        $passwordResets = DB::table(self::PASSWORD_RESET_TABLE)->get();

        foreach ($passwordResets as $reset) {
            if (Hash::check($token, $reset->token)) {
                $this->validateTokenExpiration($reset);
                return $reset;
            }
        }

        abort(back()->withErrors([
            'password' => 'Token reset password tidak valid atau sudah kadaluarsa.'
        ])->send());
    }

    /**
     * Validate token expiration
     */
    private function validateTokenExpiration($reset)
    {
        $createdAt = Carbon::parse($reset->created_at);
        
        if ($createdAt->addMinutes(self::TOKEN_EXPIRATION_MINUTES)->isPast()) {
            $this->deletePasswordResetToken($reset->email);
            
            abort(back()->withErrors([
                'password' => 'Token reset password sudah kadaluarsa. Silakan request ulang.'
            ])->send());
        }
    }

    /**
     * Update resepsionis password
     */
    private function updateResepsionisPassword($email, $password)
    {
        $resepsionis = Resepsionis::where('email_resepsionis', $email)->first();

        if (!$resepsionis) {
            abort(back()->withErrors([
                'password' => 'Data resepsionis tidak ditemukan.'
            ])->send());
        }

        $resepsionis->password_resepsionis = Hash::make($password);
        $resepsionis->save();

        return $resepsionis;
    }

    /**
     * Delete password reset token
     */
    private function deletePasswordResetToken($email)
    {
        DB::table(self::PASSWORD_RESET_TABLE)
            ->where('email', $email)
            ->delete();
    }
}
