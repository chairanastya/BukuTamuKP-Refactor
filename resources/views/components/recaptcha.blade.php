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
         data-error-callback="onRecaptchaError">
    </div>
</div>

@once
    @push('head')
        <script>
            function onRecaptchaSuccess(token) {
                console.log('[reCAPTCHA] Token received:', token ? token.substring(0, 20) + '...' : 'empty');
                // Token is automatically placed in hidden field by Google
            }
            
            function onRecaptchaError() {
                console.error('[reCAPTCHA] Error callback triggered');
            }

            window.addEventListener('DOMContentLoaded', function() {
                console.log('[reCAPTCHA] DOM Ready - script should initialize');
                // Check if grecaptcha is available
                if (typeof grecaptcha !== 'undefined') {
                    console.log('[reCAPTCHA] grecaptcha is ready');
                } else {
                    console.warn('[reCAPTCHA] grecaptcha not yet available');
                }
            });
        </script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endonce
