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

        // Check if request is secure (HTTPS) - Railway sets X-Forwarded-Proto header
        $isSecure = $request->secure() || 
                   $request->header('X-Forwarded-Proto') === 'https' ||
                   $request->header('X-Forwarded-Ssl') === 'on';
        
        // Always add security headers when using HTTPS or in production
        if ($isSecure || config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            // Strict Transport Security (HSTS) - only if HTTPS
            if ($isSecure) {
                $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
            }
            
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
            
            // Content Security Policy - ONLY HTTPS resources (no HTTP - prevents mixed content!)
            // Note: 'unsafe-inline' is needed for inline scripts, but we should minimize this
            $csp = "default-src 'self' https:; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; " .
                   "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; " .
                   "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
                   "img-src 'self' data: https:; " .  // REMOVED http: - only HTTPS images (prevents mixed content!)
                   "connect-src 'self' https:; " .    // REMOVED http: - only HTTPS connections
                   "frame-ancestors 'self'; " .
                   "base-uri 'self';";  // Prevent base tag injection
            $response->headers->set('Content-Security-Policy', $csp);
            
            // Also set X-Content-Security-Policy for older browsers
            $response->headers->set('X-Content-Security-Policy', $csp);
        }

        return $response;
    }
}