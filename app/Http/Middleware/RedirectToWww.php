<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirect apex domain (fitmatch.org.il) to www (www.fitmatch.org.il)
 * 
 * This middleware ensures all traffic goes to the canonical www subdomain.
 * Only redirects if the request is for the apex domain (not already www).
 */
class RedirectToWww
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // Only redirect if request is for apex domain (not www, not localhost, not Railway subdomain)
        if ($host === 'fitmatch.org.il') {
            $url = $request->url();
            $wwwUrl = str_replace('https://fitmatch.org.il', 'https://www.fitmatch.org.il', $url);
            $wwwUrl = str_replace('http://fitmatch.org.il', 'https://www.fitmatch.org.il', $wwwUrl);
            
            return redirect($wwwUrl, 301);
        }
        
        return $next($request);
    }
}
