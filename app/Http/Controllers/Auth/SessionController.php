<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Resepsionis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class SessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
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
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'g-recaptcha-response' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'g-recaptcha-response.required' => 'Silakan verifikasi bahwa Anda bukan robot',
        ]);

        // Verify reCAPTCHA
        $recaptchaResponse = $request->input('g-recaptcha-response');
        $recaptchaSecret = config('services.recaptcha.secret_key');

        // DEVELOPMENT BYPASS: Skip verification if using test keys in local environment
        $isTestKey = $recaptchaSecret === '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
        $isLocal = config('app.env') === 'local';

        if ($isTestKey && $isLocal) {
            \Log::info('reCAPTCHA: Using test keys in local environment - bypassing verification');
            // Skip to authentication
        } else {
            // Log detail token untuk debugging
            \Log::info('reCAPTCHA Token Details', [
                'token_length' => strlen($recaptchaResponse ?? ''),
                'token_preview' => substr($recaptchaResponse ?? '', 0, 50) . '...',
                'client_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            try {
                $httpResponse = Http::post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $recaptchaSecret,
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->ip(),
                ]);

                /** @var array{success: bool, challenge_ts?: string, hostname?: string, error-codes?: array<string>} $recaptchaData */
                $recaptchaData = $httpResponse->json();

                // Log untuk debugging
                \Log::info('reCAPTCHA Response', [
                    'success' => $recaptchaData['success'] ?? false,
                    'error-codes' => $recaptchaData['error-codes'] ?? [],
                    'hostname' => $recaptchaData['hostname'] ?? null,
                    'challenge_ts' => $recaptchaData['challenge_ts'] ?? null,
                ]);

                if (!isset($recaptchaData['success']) || !$recaptchaData['success']) {
                    $errorCodes = $recaptchaData['error-codes'] ?? [];
                    $errorMessage = 'Verifikasi reCAPTCHA gagal. ';

                    if (in_array('invalid-input-secret', $errorCodes)) {
                        $errorMessage .= 'Secret key tidak valid.';
                    } elseif (in_array('invalid-input-response', $errorCodes)) {
                        $errorMessage .= 'Response token tidak valid.';
                    } elseif (in_array('timeout-or-duplicate', $errorCodes)) {
                        $errorMessage .= 'Token sudah kadaluarsa atau sudah digunakan.';
                    } else {
                        $errorMessage .= 'Silakan coba lagi.';
                    }

                    return back()->withErrors([
                        'g-recaptcha-response' => $errorMessage,
                    ])->onlyInput('email');
                }
            } catch (\Exception $e) {
                \Log::error('reCAPTCHA Exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                return back()->withErrors([
                    'g-recaptcha-response' => 'Gagal melakukan verifikasi reCAPTCHA. Silakan coba lagi.',
                ])->onlyInput('email');
            }
        }

        $resepsionis = Resepsionis::where('email_resepsionis', $request->email)->first();

        if ($resepsionis && Hash::check($request->password, $resepsionis->password_resepsionis)) {
            Auth::guard('resepsionis')->login($resepsionis, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('resepsionis.dashboard'))
                ->with('success', 'Selamat datang, ' . $resepsionis->nama_resepsionis);
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('resepsionis')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('resepsionis.login')
            ->with('success', 'Anda berhasil logout');
    }
}
