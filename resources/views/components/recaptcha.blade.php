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
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@endonce
