<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    /**
     * Supported locales for the sitemap
     */
    private const SUPPORTED_LOCALES = ['he', 'en', 'ru', 'ar'];

    /**
     * Single source of truth: All public routes that should be in the sitemap
     * 
     * Format: 'path' => ['priority' => '0.8', 'changefreq' => 'monthly']
     * 
     * Note: Dynamic routes (like trainer profiles) are handled separately
     */
    private function getPublicRoutes(): array
    {
        return [
            '/' => ['priority' => '1.0', 'changefreq' => 'daily'],
            '/trainers' => ['priority' => '0.9', 'changefreq' => 'weekly'],
            '/about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            '/faq' => ['priority' => '0.8', 'changefreq' => 'monthly'],
            '/contact' => ['priority' => '0.7', 'changefreq' => 'monthly'],
            '/privacy' => ['priority' => '0.5', 'changefreq' => 'yearly'],
            '/terms' => ['priority' => '0.5', 'changefreq' => 'yearly'],
        ];
    }

    /**
     * Generate the main sitemap.xml
     * 
     * This is a stateless route that works even if database is unavailable
     */
    public function index(): Response
    {
        try {
            $baseUrl = rtrim(config('app.url'), '/');
            $locales = self::SUPPORTED_LOCALES;
            
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
            
            // Add all static public routes
            $publicRoutes = $this->getPublicRoutes();
            foreach ($publicRoutes as $path => $config) {
                $lastmod = ($path === '/' || $path === '/trainers') ? now() : now()->subDays(30);
                $xml .= $this->generateUrlEntry($path, $config['priority'], $config['changefreq'], $lastmod, $locales, $baseUrl);
            }
            
            // Add dynamic trainer profiles (if database is available)
            try {
                $trainers = $this->getApprovedTrainers();
                
                foreach ($trainers as $trainer) {
                    $path = '/trainers/' . $trainer->id;
                    $lastmod = $trainer->updated_at ?? $trainer->created_at ?? now();
                    $xml .= $this->generateUrlEntry($path, '0.7', 'monthly', $lastmod, $locales, $baseUrl);
                }
            } catch (\Exception $e) {
                // Log but continue - sitemap should work even if DB is unavailable
                Log::warning('Could not fetch trainers for sitemap: ' . $e->getMessage());
            }
            
            $xml .= '</urlset>';
            
            return response($xml, 200)
                ->header('Content-Type', 'application/xml; charset=utf-8')
                ->header('Cache-Control', 'public, max-age=3600');
                
        } catch (\Exception $e) {
            Log::error('Sitemap generation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return valid XML even on error
            $errorXml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $errorXml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            $errorXml .= '  <error>' . htmlspecialchars($e->getMessage(), ENT_XML1, 'UTF-8') . '</error>' . "\n";
            $errorXml .= '</urlset>';
            
            return response($errorXml, 500)
                ->header('Content-Type', 'application/xml; charset=utf-8');
        }
    }

    /**
     * Get approved trainers for sitemap
     */
    private function getApprovedTrainers()
    {
        $query = Trainer::where('approved_by_admin', true);
        
        if (Schema::hasColumn('trainers', 'status')) {
            $query->whereIn('status', ['active', 'trial']);
        }
        
        return $query->get();
    }

    /**
     * Generate a URL entry with hreflang tags for all language versions
     * 
     * @param string $path The route path (e.g., '/about', '/trainers/123')
     * @param string $priority Priority (0.0 to 1.0)
     * @param string $changefreq Change frequency (always, hourly, daily, weekly, monthly, yearly, never)
     * @param \Illuminate\Support\Carbon|string $lastmod Last modification date
     * @param array $locales Supported locales
     * @param string $baseUrl Base URL (e.g., 'https://fitmatch.org.il')
     * @return string XML string for the URL entry
     */
    private function generateUrlEntry(string $path, string $priority, string $changefreq, $lastmod, array $locales, string $baseUrl): string
    {
        // Normalize path
        $path = '/' . ltrim($path, '/');
        
        // Ensure lastmod is a Carbon instance
        if (!$lastmod instanceof \Illuminate\Support\Carbon) {
            $lastmod = now();
        }
        
        // Canonical URL (Hebrew with /he/ prefix for SEO)
        $canonicalUrl = $baseUrl . '/he' . $path;
        
        $xml = '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($canonicalUrl, ENT_XML1, 'UTF-8') . '</loc>' . "\n";
        $xml .= '    <lastmod>' . $lastmod->toAtomString() . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . htmlspecialchars($changefreq, ENT_XML1, 'UTF-8') . '</changefreq>' . "\n";
        $xml .= '    <priority>' . htmlspecialchars($priority, ENT_XML1, 'UTF-8') . '</priority>' . "\n";
        
        // Add hreflang tags for all language versions
        foreach ($locales as $locale) {
            $langUrl = $baseUrl . '/' . $locale . $path;
            $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($locale, ENT_XML1, 'UTF-8') . '" href="' . htmlspecialchars($langUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
        }
        
        // Add hreflang for backward-compatible URL (without language prefix - defaults to Hebrew)
        $backwardCompatUrl = $baseUrl . $path;
        if ($backwardCompatUrl !== $canonicalUrl) {
            $xml .= '    <xhtml:link rel="alternate" hreflang="he" href="' . htmlspecialchars($backwardCompatUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
        }
        
        // Add x-default (pointing to Hebrew canonical URL)
        $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($canonicalUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
        
        $xml .= '  </url>' . "\n";
        
        return $xml;
    }
}
