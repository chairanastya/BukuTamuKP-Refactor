<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Resepsionis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ResepsionisAccountController extends Controller
{
    /**
     * Display the account creation form.
     */
    public function create(string $token): View
    {
        $resepsionis = Resepsionis::where('token_setup', $token)
            ->where('token_setup_expired_at', '>', now())
            ->first();

        if (!$resepsionis) {
            return view('resepsionis.account-setup-error', [
                'message' => 'Link tidak valid atau sudah kedaluwarsa. Silakan hubungi administrator untuk mendapatkan link baru.'
            ]);
        }

        // Check if password already set
        if ($resepsionis->password_resepsionis) {
            return view('resepsionis.account-setup-error', [
                'message' => 'Akun Anda sudah aktif. Silakan login menggunakan email dan password yang telah Anda buat.'
            ]);
        }

        return view('resepsionis.account-setup', [
            'token' => $token,
            'email' => $resepsionis->email_resepsionis,
            'nama' => $resepsionis->nama_resepsionis,
        ]);
    }

    /**
     * Handle the account creation form submission.
     */
    public function store(Request $request, string $token): RedirectResponse
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
            'g-recaptcha-response' => 'required',
        ], [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'g-recaptcha-response.required' => 'Silakan verifikasi bahwa Anda bukan robot',
        ]);

        // Verify reCAPTCHA
        if (!$this->verifyRecaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            return back()->withErrors([
                'g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
            ])->withInput();
        }

        $resepsionis = Resepsionis::where('token_setup', $token)
            ->where('token_setup_expired_at', '>', now())
            ->first();

        if (!$resepsionis) {
            return back()->withErrors([
                'error' => 'Link tidak valid atau sudah kedaluwarsa'
            ]);
        }

        if ($resepsionis->password_resepsionis) {
            return redirect()->route('resepsionis.login')
                ->withErrors(['error' => 'Akun sudah aktif, silakan login']);
        }

        // Set password and clear token
        $resepsionis->password_resepsionis = Hash::make($request->password);
        $resepsionis->token_setup = null;
        $resepsionis->token_setup_expired_at = null;
        $resepsionis->save();

        return redirect()->route('resepsionis.login')
            ->with('status', 'Akun berhasil dibuat! Silakan login dengan email dan password yang telah Anda buat.');
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

            $httpResponse = Http::post('https://www.google.com/recaptcha/api/siteverify', [
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
