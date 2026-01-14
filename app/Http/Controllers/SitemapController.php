<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    /**
     * Supported locales
     */
    private const LOCALES = ['he', 'en', 'ru', 'ar'];

    /**
     * Generate sitemap.xml
     */
    public function index(): Response
    {
        try {
            // Log request start
            \Log::info('SitemapController@index called', [
                'uri' => request()->getRequestUri(),
                'method' => request()->getMethod(),
                'ip' => request()->ip(),
                'host' => request()->getHost(),
                'user_agent' => request()->userAgent(),
            ]);
            
            $baseUrl = rtrim(config('app.url'), '/');
            \Log::debug('SitemapController: baseUrl', ['baseUrl' => $baseUrl]);
            
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
            
            // Static pages
            $pages = [
                '/' => ['priority' => '1.0', 'changefreq' => 'daily'],
                '/trainers' => ['priority' => '0.9', 'changefreq' => 'weekly'],
                '/about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
                '/faq' => ['priority' => '0.8', 'changefreq' => 'monthly'],
                '/contact' => ['priority' => '0.7', 'changefreq' => 'monthly'],
                '/privacy' => ['priority' => '0.5', 'changefreq' => 'yearly'],
                '/terms' => ['priority' => '0.5', 'changefreq' => 'yearly'],
            ];
            
            // Generate URL entry for each language version of each page
            foreach ($pages as $path => $config) {
                $lastmod = ($path === '/' || $path === '/trainers') ? now() : now()->subDays(30);
                
                // Create separate URL entry for each language
                foreach (self::LOCALES as $locale) {
                    $xml .= $this->urlEntryForLocale($path, $locale, $config['priority'], $config['changefreq'], $lastmod, $baseUrl);
                }
                
                // Also add backward-compatible URL (without prefix - defaults to Hebrew)
                $xml .= $this->urlEntryForLocale($path, null, $config['priority'], $config['changefreq'], $lastmod, $baseUrl);
            }
            
            $totalStaticUrls = count($pages) * (count(self::LOCALES) + 1);
            \Log::debug('SitemapController: Static pages added', ['count' => $totalStaticUrls]);
            
            // Trainer profiles
            try {
                $query = Trainer::where('approved_by_admin', true);
                if (Schema::hasColumn('trainers', 'status')) {
                    $query->whereIn('status', ['active', 'trial']);
                }
                $trainers = $query->get();
                
                \Log::debug('SitemapController: Trainers found', ['count' => $trainers->count()]);
                
                foreach ($trainers as $trainer) {
                    $path = '/trainers/' . $trainer->id;
                    $lastmod = $trainer->updated_at ?? $trainer->created_at ?? now();
                    
                    // Create separate URL entry for each language
                    foreach (self::LOCALES as $locale) {
                        $xml .= $this->urlEntryForLocale($path, $locale, '0.7', 'monthly', $lastmod, $baseUrl);
                    }
                    
                    // Also add backward-compatible URL (without prefix - defaults to Hebrew)
                    $xml .= $this->urlEntryForLocale($path, null, '0.7', 'monthly', $lastmod, $baseUrl);
                }
            } catch (\Exception $e) {
                \Log::warning('SitemapController: Error fetching trainers', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            
            $xml .= '</urlset>';
            
            \Log::info('SitemapController@index completed successfully', [
                'xml_length' => strlen($xml),
                'url_count' => substr_count($xml, '<url>'),
            ]);
            
            return response($xml, 200)
                ->header('Content-Type', 'application/xml; charset=utf-8')
                ->header('Cache-Control', 'public, max-age=3600');
                
        } catch (\Exception $e) {
            \Log::error('SitemapController@index ERROR', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return 500 with error details in production debug mode
            if (config('app.debug')) {
                return response($e->getMessage() . "\n" . $e->getTraceAsString(), 500)
                    ->header('Content-Type', 'text/plain');
            }
            
            abort(500, 'Sitemap generation failed');
        }
    }

    /**
     * Generate URL entry for a specific locale with hreflang tags
     * 
     * @param string $path The page path (e.g., '/about')
     * @param string|null $locale The locale (e.g., 'he', 'en', 'ru', 'ar') or null for backward-compatible URL
     * @param string $priority
     * @param string $changefreq
     * @param mixed $lastmod
     * @param string $baseUrl
     * @return string
     */
    private function urlEntryForLocale(string $path, ?string $locale, string $priority, string $changefreq, $lastmod, string $baseUrl): string
    {
        $path = '/' . ltrim($path, '/');
        
        if (!$lastmod instanceof \Illuminate\Support\Carbon) {
            $lastmod = now();
        }
        
        // Build URL with locale prefix (or without prefix if locale is null)
        if ($locale === null) {
            $canonical = $baseUrl . $path;
        } else {
            $canonical = $baseUrl . '/' . $locale . $path;
        }
        
        $xml = '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($canonical, ENT_XML1, 'UTF-8') . '</loc>' . "\n";
        $xml .= '    <lastmod>' . $lastmod->toAtomString() . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . htmlspecialchars($changefreq, ENT_XML1, 'UTF-8') . '</changefreq>' . "\n";
        $xml .= '    <priority>' . htmlspecialchars($priority, ENT_XML1, 'UTF-8') . '</priority>' . "\n";
        
        // Hreflang tags for all languages
        foreach (self::LOCALES as $lang) {
            $url = $baseUrl . '/' . $lang . $path;
            $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($lang, ENT_XML1, 'UTF-8') . '" href="' . htmlspecialchars($url, ENT_XML1, 'UTF-8') . '" />' . "\n";
        }
        
        // Backward-compatible URL (without prefix) - only if current entry is not already the backward-compatible one
        if ($locale !== null) {
            $backwardUrl = $baseUrl . $path;
            $xml .= '    <xhtml:link rel="alternate" hreflang="he" href="' . htmlspecialchars($backwardUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
        }
        
        // x-default always points to Hebrew version
        $hebrewUrl = $baseUrl . '/he' . $path;
        $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($hebrewUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
        
        $xml .= '  </url>' . "\n";
        
        return $xml;
    }
}
