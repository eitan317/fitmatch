<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add security headers in production or when forced
        if (config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            // Strict Transport Security (HSTS)
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            
            // Prevent clickjacking
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            
            // XSS Protection
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            
            // Content Type Options - prevent MIME sniffing
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            
            // Referrer Policy
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // Permissions Policy (formerly Feature-Policy)
            $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
            
            // Content Security Policy - allow only HTTPS resources
            $csp = "default-src 'self' https:; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; " .
                   "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; " .
                   "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
                   "img-src 'self' data: https: http:; " .
                   "connect-src 'self' https:; " .
                   "frame-ancestors 'self';";
            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}