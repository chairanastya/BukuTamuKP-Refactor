<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Resepsionis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
            'g-recaptcha-response' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'g-recaptcha-response.required' => 'Silakan verifikasi bahwa Anda bukan robot',
        ]);

        // Verify reCAPTCHA
        if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
            ])->onlyInput('email');
        }

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

    private function verifyRecaptcha(string $response, string $remoteip): bool
    {
        try {
            $recaptchaSecret = config('services.recaptcha.secret_key');
            $isTestKey = $recaptchaSecret === '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
            $isLocal = config('app.env') === 'local';

            if ($isTestKey && $isLocal) {
                \Log::info('reCAPTCHA: Using test keys in local environment - bypassing verification');
                return true;
            }

            $httpResponse = Http::timeout(10)->retry(2, 1000)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $response,
                'remoteip' => $remoteip,
            ]);

            $recaptchaData = $httpResponse->json();
            return isset($recaptchaData['success']) && $recaptchaData['success'];
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
