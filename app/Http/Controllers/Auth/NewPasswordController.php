<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Resepsionis;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class NewPasswordController extends Controller
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
     * Display the password reset view.
     */
    public function create(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
            'g-recaptcha-response' => 'required',
        ], [
            'token.required' => 'Token reset password tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'g-recaptcha-response.required' => 'Silakan verifikasi bahwa Anda bukan robot',
        ]);

        // Verify reCAPTCHA
        if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
            ])->withInput();
        }

        $passwordReset = $this->findValidPasswordReset($request->token);
        $this->updateResepsionisPassword($passwordReset->email, $request->password);
        $this->deletePasswordResetToken($passwordReset->email);

        return redirect()->route('resepsionis.login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Find valid password reset by token
     */
    private function findValidPasswordReset(string $token): object
    {
        $passwordResets = DB::table(self::PASSWORD_RESET_TABLE)->get();

        foreach ($passwordResets as $reset) {
            if (Hash::check($token, $reset->token)) {
                $this->validateTokenExpiration($reset);
                return $reset;
            }
        }

        abort(redirect()->back()->withErrors([
            'password' => 'Token reset password tidak valid atau sudah kadaluarsa.'
        ]));
    }

    /**
     * Validate token expiration
     */
    private function validateTokenExpiration(object $reset): void
    {
        $createdAt = Carbon::parse($reset->created_at);

        if ($createdAt->addMinutes(self::TOKEN_EXPIRATION_MINUTES)->isPast()) {
            $this->deletePasswordResetToken($reset->email);

            abort(redirect()->back()->withErrors([
                'password' => 'Token reset password sudah kadaluarsa. Silakan request ulang.'
            ]));
        }
    }

    /**
     * Update resepsionis password
     */
    private function updateResepsionisPassword(string $email, string $password): void
    {
        $resepsionis = Resepsionis::where('email_resepsionis', $email)->first();

        if (!$resepsionis) {
            abort(redirect()->back()->withErrors([
                'password' => 'Data resepsionis tidak ditemukan.'
            ]));
        }

        $resepsionis->password_resepsionis = Hash::make($password);
        $resepsionis->save();
    }

    /**
     * Delete password reset token
     */
    private function deletePasswordResetToken(string $email): void
    {
        DB::table(self::PASSWORD_RESET_TABLE)
            ->where('email', $email)
            ->delete();
    }

    private function verifyRecaptcha(string $response, string $remoteip): bool
    {
        try {
            $recaptchaSecret = config('services.recaptcha.secret_key');

            // Skip verification if disabled via config
            if (!config('services.recaptcha.verify', true)) {
                \Log::info('reCAPTCHA: Verification disabled via config - bypassing');
                return true;
            }

            // Skip if using Google's official testing keys
            $testingKeys = [
                '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI',
                '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe',
            ];
            if (in_array($recaptchaSecret, $testingKeys)) {
                \Log::info('reCAPTCHA: Using test keys - bypassing verification');
                return true;
            }

            $httpResponse = Http::timeout(10)->retry(2, 1000)->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
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
