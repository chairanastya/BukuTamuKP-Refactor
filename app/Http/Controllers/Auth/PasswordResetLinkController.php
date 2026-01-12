<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Resepsionis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Password reset tokens table name
     */
    private const PASSWORD_RESET_TABLE = 'password_reset_tokens';

    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        $resepsionis = Resepsionis::where('email_resepsionis', $request->email)->first();

        if (!$resepsionis) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan dalam sistem kami.',
            ])->onlyInput('email');
        }

        $token = $this->createPasswordResetToken($request->email);
        $this->sendPasswordResetEmail($request->email, $token);

        return back()->with('status', 'Link reset password telah dikirim ke email Anda! Silakan cek inbox atau folder spam.');
    }

    /**
     * Create password reset token
     */
    private function createPasswordResetToken(string $email): string
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
    private function sendPasswordResetEmail(string $email, string $token): void
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

            throw new \Exception('Gagal mengirim email. Pastikan konfigurasi email sudah benar.');
        }
    }
}
