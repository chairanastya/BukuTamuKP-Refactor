<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddContentSecurityPolicyHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add Content-Security-Policy header to allow Google reCAPTCHA
        $csp = "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' https://www.google.com/recaptcha/api.js https://recaptcha.net/recaptcha/api.js; "
            . "frame-src 'self' https://www.google.com/recaptcha/ https://recaptcha.net/recaptcha/; "
            . "connect-src 'self' https://www.google.com/recaptcha/api/siteverify https://recaptcha.net/recaptcha/api/siteverify; "
            . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
            . "font-src 'self' https://fonts.gstatic.com; "
            . "img-src 'self' data: https:;";

        $response->headers->set('Content-Security-Policy-Report-Only', $csp);

        return $response;
    }
}
