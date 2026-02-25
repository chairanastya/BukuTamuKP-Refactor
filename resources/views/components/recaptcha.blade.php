@props([
    'theme' => 'light',
    'size' => 'normal',
])

<div {{ $attributes->merge(['class' => 'flex justify-center']) }}>
    <div class="g-recaptcha" 
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

            function onRecaptchaSuccess(token) {
                console.log('[reCAPTCHA] ✓ Token received and set');
                window.recaptchaTokenReady = true;
                const statusDiv = document.getElementById('recaptcha-status');
                if (statusDiv) {
                    statusDiv.classList.remove('hidden');
                    statusDiv.classList.add('text-green-600');
                }
                console.log('[reCAPTCHA] Token field value present:', !!document.querySelector('[name="g-recaptcha-response"]')?.value);
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
                console.warn('[reCAPTCHA] ⚠ Token expired - need to verify again');
                window.recaptchaTokenReady = false;
                const statusDiv = document.getElementById('recaptcha-status');
                if (statusDiv) {
                    statusDiv.classList.add('hidden');
                }
            }

            // Google calls this when the API is ready
            function onRecaptchaLoad() {
                console.log('[reCAPTCHA] ✓ Google API loaded and ready');
                window.recaptchaApiReady = true;
                setupFormValidation();
            }

            function setupFormValidation() {
                console.log('[reCAPTCHA] Setting up form validation');
                const forms = document.querySelectorAll('form');
                forms.forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const tokenField = document.querySelector('[name="g-recaptcha-response"]');
                        const tokenValue = tokenField ? tokenField.value : '';
                        console.log('[reCAPTCHA] Form submit check - token present:', !!tokenValue);
                        
                        if (!tokenValue) {
                            console.warn('[reCAPTCHA] ✗ Token missing! Form blocked.');
                            e.preventDefault();
                            alert('Silakan verifikasi reCAPTCHA terlebih dahulu');
                            return false;
                        }
                        console.log('[reCAPTCHA] ✓ Token found, allowing form submission');
                    });
                });
            }

            // Fallback: if DOMContentLoaded fires before onload callback
            window.addEventListener('DOMContentLoaded', function() {
                console.log('[reCAPTCHA] DOMContentLoaded fired');
                if (window.recaptchaApiReady) {
                    console.log('[reCAPTCHA] API already ready');
                } else {
                    console.log('[reCAPTCHA] Waiting for API to load...');
                    // Retry setup after a delay
                    setTimeout(() => {
                        if (typeof grecaptcha !== 'undefined') {
                            console.log('[reCAPTCHA] ✓ grecaptcha now available');
                            setupFormValidation();
                        }
                    }, 500);
                }
            });
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=implicit" async defer></script>
    @endpush
@endonce
