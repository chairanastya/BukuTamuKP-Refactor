@props([
    'theme' => 'light',
    'size' => 'normal',
])

<div {{ $attributes->merge(['class' => 'flex justify-center']) }}>
    <div class="g-recaptcha" 
         id="main-recaptcha"
         data-sitekey="{{ config('services.recaptcha.site_key') }}"
         data-theme="{{ $theme }}"
         data-size="{{ $size }}"
         data-callback="onRecaptchaSuccess"
         data-error-callback="onRecaptchaError"
         data-expired-callback="onRecaptchaExpired">
    </div>
    <div id="recaptcha-status" class="mt-2 text-sm text-gray-500 hidden">
        ✓ Verifikasi reCAPTCHA berhasil
    </div>
</div>

@once
    @push('head')
        <script>
            window.recaptchaTokenReady = false;
            window.recaptchaApiReady = false;
            window.recaptchaTokenTimestamp = null;
            window.recaptchaWidgetId = null;
            const TOKEN_EXPIRY_MS = 90000; // 90 seconds - refresh before 2 min expires

            function onRecaptchaSuccess(token) {
                console.log('[reCAPTCHA] ✓ Token received and set');
                window.recaptchaTokenReady = true;
                window.recaptchaTokenTimestamp = Date.now();
                const statusDiv = document.getElementById('recaptcha-status');
                if (statusDiv) {
                    statusDiv.classList.remove('hidden');
                    statusDiv.classList.add('text-green-600');
                }
                const tokenField = document.querySelector('[name="g-recaptcha-response"]');
                console.log('[reCAPTCHA] Token length:', tokenField?.value?.length || 0);
            }
            
            function onRecaptchaError() {
                console.error('[reCAPTCHA] ✗ Error callback triggered');
                window.recaptchaTokenReady = false;
                const statusDiv = document.getElementById('recaptcha-status');
                if (statusDiv) {
                    statusDiv.classList.add('hidden');
                }
            }

            function onRecaptchaExpired() {
                console.warn('[reCAPTCHA] ⚠ Token expired - must be checked again');
                window.recaptchaTokenReady = false;
                window.recaptchaTokenTimestamp = null;
                const statusDiv = document.getElementById('recaptcha-status');
                if (statusDiv) {
                    statusDiv.classList.add('hidden');
                }
            }

            function onRecaptchaLoad() {
                console.log('[reCAPTCHA] ✓ Google API loaded and ready');
                window.recaptchaApiReady = true;
                // Get widget ID for the main recaptcha
                if (typeof grecaptcha !== 'undefined') {
                    const captchaDiv = document.getElementById('main-recaptcha');
                    if (captchaDiv && captchaDiv._recaptchaId !== undefined) {
                        window.recaptchaWidgetId = captchaDiv._recaptchaId;
                        console.log('[reCAPTCHA] Widget ID:', window.recaptchaWidgetId);
                    }
                }
                setupFormValidation();
            }

            function setupFormValidation() {
                console.log('[reCAPTCHA] Setting up form validation');
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const tokenField = document.querySelector('[name="g-recaptcha-response"]');
                        const tokenValue = tokenField ? tokenField.value : '';
                        const now = Date.now();
                        const tokenAge = window.recaptchaTokenTimestamp ? now - window.recaptchaTokenTimestamp : 0;
                        
                        console.log('[reCAPTCHA] Form submit check:');
                        console.log('  - Token present:', !!tokenValue);
                        console.log('  - Token age (ms):', tokenAge);
                        console.log('  - Token expires in (ms):', TOKEN_EXPIRY_MS - tokenAge);
                        
                        if (!tokenValue) {
                            console.warn('[reCAPTCHA] ✗ Token missing! Form blocked.');
                            e.preventDefault();
                            alert('Silakan verifikasi reCAPTCHA terlebih dahulu');
                            return false;
                        }
                        
                        // Warn if token is getting old
                        if (tokenAge > 60000) {
                            console.warn('[reCAPTCHA] ⚠ Token is older than 60 seconds, refresh recommended');
                            // Auto-refresh if too old
                            if (tokenAge > TOKEN_EXPIRY_MS) {
                                console.error('[reCAPTCHA] ✗ Token likely expired!');
                                e.preventDefault();
                                alert('Token reCAPTCHA telah kadaluarsa. Silakan verifikasi kembali.');
                                if (window.recaptchaApiReady && typeof grecaptcha !== 'undefined') {
                                    grecaptcha.reset(window.recaptchaWidgetId);
                                }
                                return false;
                            }
                        }
                        
                        console.log('[reCAPTCHA] ✓ Token valid, allowing submission');
                    });
                });
            }

            window.addEventListener('DOMContentLoaded', function() {
                console.log('[reCAPTCHA] DOMContentLoaded fired');
                setTimeout(() => {
                    if (typeof grecaptcha !== 'undefined') {
                        console.log('[reCAPTCHA] ✓ grecaptcha available');
                        setupFormValidation();
                    } else {
                        console.warn('[reCAPTCHA] grecaptcha not available');
                    }
                }, 100);
            });
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=implicit" async defer></script>
    @endpush
@endonce
