@props([
    'theme' => 'light',
    'size' => 'normal',
])

<div {{ $attributes->merge(['class' => 'flex justify-center']) }}>
    <div class="g-recaptcha" 
         data-sitekey="{{ config('services.recaptcha.site_key') }}"
         data-theme="{{ $theme }}"
         data-size="{{ $size }}">
    </div>
</div>

@once
    @push('head')
        <script>
            // Prevent Google script from running before DOM is ready
            window.__RECAPTCHA_DEFER__ = true;
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=implicit" async defer></script>
        <script>
            // Callback when Google API is ready
            window.onRecaptchaLoad = function() {
                console.log('[reCAPTCHA] Google API loaded');
                // Defer rendering to next tick
                setTimeout(function() {
                    if (typeof grecaptcha !== 'undefined') {
                        console.log('[reCAPTCHA] Ready to use');
                    }
                }, 100);
            };
            
            // Ensure div is rendered before Google script runs
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    window.__RECAPTCHA_DEFER__ = false;
                });
            } else {
                window.__RECAPTCHA_DEFER__ = false;
            }
        </script>
    @endpush
@endonce
