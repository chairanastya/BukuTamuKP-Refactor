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
            window.recaptchaTokenTimestamp = null;
            window.recaptchaFormListenersAttached = false;
            const TOKEN_EXPIRY_MS = 90000;

            function onRecaptchaSuccess(token) {
                window.recaptchaTokenTimestamp = Date.now();
                const statusDiv = document.getElementById('recaptcha-status');
                if (statusDiv) {
                    statusDiv.classList.remove('hidden');
                    statusDiv.classList.add('text-green-600');
                }
            }

            function onRecaptchaError() {
                window.recaptchaTokenTimestamp = null;
                const statusDiv = document.getElementById('recaptcha-status');
                if (statusDiv) statusDiv.classList.add('hidden');
            }

            function onRecaptchaExpired() {
                window.recaptchaTokenTimestamp = null;
                const statusDiv = document.getElementById('recaptcha-status');
                if (statusDiv) statusDiv.classList.add('hidden');
            }

            function onRecaptchaLoad() {
                if (window.recaptchaFormListenersAttached) return;
                window.recaptchaFormListenersAttached = true;

                document.querySelectorAll('form').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        const tokenField = document.querySelector('[name="g-recaptcha-response"]');
                        const tokenValue = tokenField ? tokenField.value : '';
                        const tokenAge = window.recaptchaTokenTimestamp ? Date.now() - window.recaptchaTokenTimestamp : 0;

                        if (!tokenValue) {
                            e.preventDefault();
                            alert('Silakan verifikasi reCAPTCHA terlebih dahulu');
                            return false;
                        }

                        if (tokenAge > TOKEN_EXPIRY_MS) {
                            e.preventDefault();
                            alert('Token reCAPTCHA telah kadaluarsa. Silakan verifikasi kembali.');
                            if (typeof grecaptcha !== 'undefined') {
                                grecaptcha.reset();
                                window.recaptchaTokenTimestamp = null;
                            }
                            return false;
                        }
                    });
                });
            }
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=implicit" async defer></script>
    @endpush
@endonce
