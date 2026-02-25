@props([
    'theme' => 'light',
    'size' => 'normal',
])

<div {{ $attributes->merge(['class' => 'flex justify-center']) }}>
    <div class="g-recaptcha" 
         id="recaptcha-container"
         data-sitekey="{{ config('services.recaptcha.site_key') }}"
         data-theme="{{ $theme }}"
         data-size="{{ $size }}"
         data-callback="onRecaptchaSuccess">
    </div>
</div>

@push('head')
    <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>
@endpush

@push('scripts')
    <script>
        // Global callback for reCAPTCHA load
        window.onRecaptchaLoad = function() {
            console.log('reCAPTCHA API loaded');
            // Force render if container exists
            var container = document.getElementById('recaptcha-container');
            if (container && typeof grecaptcha !== 'undefined') {
                try {
                    grecaptcha.render('recaptcha-container', {
                        'sitekey': '{{ config('services.recaptcha.site_key') }}',
                        'theme': '{{ $theme }}',
                        'size': '{{ $size }}'
                    });
                    console.log('reCAPTCHA rendered successfully');
                } catch (e) {
                    console.error('Error rendering reCAPTCHA:', e);
                }
            }
        };

        // Callback when user completes captcha
        window.onRecaptchaSuccess = function(token) {
            console.log('reCAPTCHA token received:', token.substring(0, 20) + '...');
        };

        // Ensure grecaptcha is ready before using
        window.grecaptchaReady = function() {
            console.log('grecaptcha is ready');
            return typeof grecaptcha !== 'undefined';
        };
    </script>
@endpush
