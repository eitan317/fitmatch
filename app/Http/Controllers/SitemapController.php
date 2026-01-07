<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    /**
     * Supported locales for the sitemap
     */
    private function getSupportedLocales(): array
    {
        return ['he', 'en', 'ru', 'ar'];
    }

    /**
     * Get locale code for hreflang (ISO 639-1 format)
     */
    private function getHreflangCode(string $locale): string
    {
        return $locale; // Already in ISO 639-1 format
    }

    public function index()
    {
        try {
            $baseUrl = config('app.url');
            
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            
            // Main sitemap
            $xml .= '  <sitemap>' . "\n";
            $xml .= '    <loc>' . $baseUrl . '/sitemap.xml</loc>' . "\n";
            $xml .= '    <lastmod>' . now()->toAtomString() . '</lastmod>' . "\n";
            $xml .= '  </sitemap>' . "\n";
            
            // Trainers sitemap
            $xml .= '  <sitemap>' . "\n";
            $xml .= '    <loc>' . $baseUrl . '/sitemap-trainers.xml</loc>' . "\n";
            $xml .= '    <lastmod>' . now()->toAtomString() . '</lastmod>' . "\n";
            $xml .= '  </sitemap>' . "\n";
            
            $xml .= '</sitemapindex>';
            
            return response($xml, 200)
                ->header('Content-Type', 'application/xml; charset=utf-8')
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
        } catch (\Exception $e) {
            Log::error('Sitemap index error: ' . $e->getMessage());
            return response('<?xml version="1.0" encoding="UTF-8"?><error>' . htmlspecialchars($e->getMessage()) . '</error>', 500)
                ->header('Content-Type', 'application/xml; charset=utf-8');
        }
    }
    
    public function main()
    {
        try {
            Log::info('Sitemap main called');
            $baseUrl = rtrim(config('app.url'), '/');
            $locales = $this->getSupportedLocales();
            
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
            
            // Homepage with all language versions
            $xml .= $this->urlWithHreflang('/', '1.0', 'daily', now(), $locales, $baseUrl);
            
            // Static pages with all language versions
            $pages = [
                '/trainers' => ['priority' => '0.9', 'changefreq' => 'weekly'],
                '/about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
                '/faq' => ['priority' => '0.8', 'changefreq' => 'monthly'],
                '/contact' => ['priority' => '0.7', 'changefreq' => 'monthly'],
                '/privacy' => ['priority' => '0.5', 'changefreq' => 'yearly'],
                '/terms' => ['priority' => '0.5', 'changefreq' => 'yearly'],
            ];
            
            foreach ($pages as $page => $config) {
                $lastmod = ($page === '/trainers') ? now() : now()->subDays(7);
                $xml .= $this->urlWithHreflang($page, $config['priority'], $config['changefreq'], $lastmod, $locales, $baseUrl);
            }
            
            $xml .= '</urlset>';
            
            Log::info('Sitemap generated successfully');
            
            return response($xml, 200)
                ->header('Content-Type', 'application/xml; charset=utf-8')
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
        } catch (\Exception $e) {
            Log::error('Sitemap main error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return response('<?xml version="1.0" encoding="UTF-8"?><error>' . htmlspecialchars($e->getMessage()) . '</error>', 500)
                ->header('Content-Type', 'application/xml; charset=utf-8');
        }
    }
    
    public function trainers()
    {
        try {
            $baseUrl = rtrim(config('app.url'), '/');
            $locales = $this->getSupportedLocales();
            
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
            
            // Check if status column exists (for migration compatibility)
            $query = Trainer::where('approved_by_admin', true);
            
            if (Schema::hasColumn('trainers', 'status')) {
                $query->whereIn('status', ['active', 'trial']);
            }
            
            $trainers = $query->get();
            
            foreach ($trainers as $trainer) {
                $path = '/trainers/' . $trainer->id;
                $lastmod = $trainer->updated_at ?? $trainer->created_at ?? now();
                $xml .= $this->urlWithHreflang($path, '0.7', 'monthly', $lastmod, $locales, $baseUrl);
            }
            
            $xml .= '</urlset>';
            
            return response($xml, 200)
                ->header('Content-Type', 'application/xml; charset=utf-8')
                ->header('Cache-Control', 'public, max-age=3600'); // Cache for 1 hour
        } catch (\Exception $e) {
            Log::error('Sitemap trainers error: ' . $e->getMessage());
            return response('<?xml version="1.0" encoding="UTF-8"?><error>' . htmlspecialchars($e->getMessage()) . '</error>', 500)
                ->header('Content-Type', 'application/xml; charset=utf-8');
        }
    }
    
    /**
     * Generate URL entry with hreflang tags for all language versions
     */
    private function urlWithHreflang(string $path, string $priority, string $changefreq, $lastmod, array $locales, string $baseUrl): string
    {
        if (!$lastmod instanceof \Illuminate\Support\Carbon) {
            $lastmod = now();
        }
        
        // Normalize path
        $path = '/' . ltrim($path, '/');
        
        // Canonical URL (Hebrew with /he/ prefix for SEO consistency)
        $canonicalUrl = $baseUrl . '/he' . $path;
        
        $xml = '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($canonicalUrl, ENT_XML1, 'UTF-8') . '</loc>' . "\n";
        $xml .= '    <lastmod>' . $lastmod->toAtomString() . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $priority . '</priority>' . "\n";
        
        // Add hreflang tags for all language versions
        foreach ($locales as $locale) {
            $hreflang = $this->getHreflangCode($locale);
            
            // Build language-specific URL
            $langUrl = $baseUrl . '/' . $locale . $path;
            
            $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($hreflang, ENT_XML1, 'UTF-8') . '" href="' . htmlspecialchars($langUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
        }
        
        // Add hreflang for backward-compatible URL (Hebrew without prefix)
        $backwardCompatUrl = $baseUrl . $path;
        if ($backwardCompatUrl !== $canonicalUrl) {
            $xml .= '    <xhtml:link rel="alternate" hreflang="he" href="' . htmlspecialchars($backwardCompatUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
        }
        
        // Add x-default (pointing to Hebrew canonical URL)
        $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($canonicalUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
        
        $xml .= '  </url>' . "\n";
        
        return $xml;
    }
    
    /**
     * Legacy method for backward compatibility (not used but kept for safety)
     */
    private function url($loc, $priority, $changefreq, $lastmod)
    {
        if (!$lastmod instanceof \Illuminate\Support\Carbon) {
            $lastmod = now();
        }
        
        return '  <url>' . "\n" .
               '    <loc>' . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . '</loc>' . "\n" .
               '    <lastmod>' . $lastmod->toAtomString() . '</lastmod>' . "\n" .
               '    <changefreq>' . $changefreq . '</changefreq>' . "\n" .
               '    <priority>' . $priority . '</priority>' . "\n" .
               '  </url>' . "\n";
    }
}
