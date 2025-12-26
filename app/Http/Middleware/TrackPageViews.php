<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PageView;
use Illuminate\Support\Facades\Schema;

class TrackPageViews
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Track page views only for GET requests (not AJAX)
        if ($request->method() === 'GET' && !$request->ajax()) {
            try {
                // Skip tracking for admin routes and API routes
                $path = $request->path();
                if (!str_starts_with($path, 'admin') && !str_starts_with($path, 'api')) {
                    // Check if table exists before trying to insert
                    if (Schema::hasTable('page_views')) {
                        PageView::create([
                            'page_path' => $path,
                            'ip_address' => $request->ip(),
                            'user_agent' => $request->userAgent(),
                            'user_id' => auth()->id(),
                            'viewed_at' => now(),
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Silently fail if tracking fails (e.g., database not migrated yet)
                \Log::warning('Failed to track page view: ' . $e->getMessage());
            }
        }
        
        return $response;
    }
}
