<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'g-recaptcha-response' => ['required', 'string'],
        ];
    }

    /**
     * Custom messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => 'Silakan verifikasi bahwa Anda bukan robot',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // Validate reCAPTCHA token first
        $this->validateRecaptcha();

        $this->ensureIsNotRateLimited();

        $credentials = [
            'email_resepsionis' => $this->email,
            'password' => $this->password,
        ];

        if (!Auth::guard('resepsionis')->attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Validate reCAPTCHA response with Google servers.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateRecaptcha(): void
    {
        $recaptchaSecret = config('services.recaptcha.secret_key');
        $recaptchaToken = $this->input('g-recaptcha-response');

        if (!$recaptchaSecret || !$recaptchaToken) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Silakan verifikasi bahwa Anda bukan robot',
            ]);
        }

        // Skip Google validation if using testing keys (instant login in dev)
        if ($this->isTestingRecaptchaKey($recaptchaSecret)) {
            Log::info('reCAPTCHA testing keys detected - skipping Google validation', [
                'email' => $this->email,
            ]);
            return;
        }

        // Skip Google validation if disabled via config (e.g. for local testing)
        if (!config('services.recaptcha.verify', true)) {
            Log::info('reCAPTCHA verification disabled via config', [
                'email' => $this->email,
            ]);
            return;
        }

        try {
            $response = Http::timeout(5)
                ->asForm()
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $recaptchaSecret,
                    'response' => $recaptchaToken,
                    'remoteip' => $this->ip(),
                ]);

            $data = $response->json();

            Log::info('reCAPTCHA validation response', [
                'success' => $data['success'] ?? false,
                'score' => $data['score'] ?? null,
                'email' => $this->email,
            ]);

            // Check if reCAPTCHA validation was successful
            if (!($data['success'] ?? false)) {
                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
                ]);
            }

            // Optional: Check score if using reCAPTCHA v3 (score > 0.5 is good)
            $score = $data['score'] ?? 1.0;
            if ($score < 0.5) {
                Log::warning('reCAPTCHA score too low', [
                    'score' => $score,
                    'email' => $this->email,
                ]);

                throw ValidationException::withMessages([
                    'g-recaptcha-response' => 'Verifikasi tidak berhasil. Silakan coba lagi.',
                ]);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('reCAPTCHA connection timeout', [
                'error' => $e->getMessage(),
                'email' => $this->email,
            ]);

            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Terjadi kesalahan saat verifikasi reCAPTCHA. Silakan coba lagi.',
            ]);
        } catch (\Exception $e) {
            Log::error('reCAPTCHA validation error', [
                'error' => $e->getMessage(),
                'email' => $this->email,
            ]);

            throw ValidationException::withMessages([
                'g-recaptcha-response' => 'Terjadi kesalahan saat verifikasi. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Check if the reCAPTCHA key is a testing/example key.
     * Testing keys are provided by Google for development and don't need validation.
     */
    private function isTestingRecaptchaKey(string $key): bool
    {
        // Google's example/testing reCAPTCHA keys (from official docs)
        $testingKeys = [
            '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI', // v2 testing key
            '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe', // v2 testing secret
        ];

        return in_array($key, $testingKeys);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
