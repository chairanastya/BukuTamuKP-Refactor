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

        try {
            $httpResponse = Http::post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecret,
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip(),
            ]);

            /** @var array{success: bool, challenge_ts?: string, hostname?: string, error-codes?: array<string>} $recaptchaData */
            $recaptchaData = $httpResponse->json();

            if (!isset($recaptchaData['success']) || !$recaptchaData['success']) {
                return back()->withErrors([
                    'g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
                ])->onlyInput('email');
            }
        } catch (\Exception $e) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Gagal melakukan verifikasi reCAPTCHA. Silakan coba lagi.',
            ])->onlyInput('email');
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
