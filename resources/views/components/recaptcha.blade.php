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

            function onRecaptchaSuccess(token) {
                console.log('[reCAPTCHA] ✓ Token received and set');
                window.recaptchaTokenReady = true;
                const statusDiv = document.getElementById('recaptcha-status');
                if (statusDiv) {
                    statusDiv.classList.remove('hidden');
                    statusDiv.classList.add('text-green-600');
                }
                // Token is automatically in g-recaptcha-response hidden field
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

            // Prevent form submission without token
            window.addEventListener('DOMContentLoaded', function() {
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
                
                // Check initial state
                if (typeof grecaptcha !== 'undefined') {
                    console.log('[reCAPTCHA] ✓ grecaptcha API ready');
                } else {
                    console.warn('[reCAPTCHA] grecaptcha not available yet');
                }
            });
        </script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endonce
