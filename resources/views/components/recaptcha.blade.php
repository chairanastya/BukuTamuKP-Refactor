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

@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        // Wait for reCAPTCHA to load
        window.grecaptchaReady = false;
        window.onRecaptchaLoad = function() {
            window.grecaptchaReady = true;
            console.log('reCAPTCHA loaded successfully');
        };
        
        // Fallback check
        setTimeout(function() {
            if (!window.grecaptchaReady && typeof grecaptcha === 'undefined') {
                console.warn('reCAPTCHA might not have loaded. Attempting to reload...');
                // Reload script if not loaded
                var script = document.createElement('script');
                script.src = 'https://www.google.com/recaptcha/api.js';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            }
        }, 3000);
    </script>
@endpush
