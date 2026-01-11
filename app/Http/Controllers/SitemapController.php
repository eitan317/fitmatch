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
        // Log request to verify it reaches Laravel (temporary debugging)
        \Log::info('SitemapController@index called');
        
        $baseUrl = rtrim(config('app.url'), '/');
        
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
        
        foreach ($pages as $path => $config) {
            $lastmod = ($path === '/' || $path === '/trainers') ? now() : now()->subDays(30);
            $xml .= $this->urlEntry($path, $config['priority'], $config['changefreq'], $lastmod, $baseUrl);
        }
        
        // Trainer profiles
        try {
            $query = Trainer::where('approved_by_admin', true);
            if (Schema::hasColumn('trainers', 'status')) {
                $query->whereIn('status', ['active', 'trial']);
            }
            $trainers = $query->get();
            
            foreach ($trainers as $trainer) {
                $path = '/trainers/' . $trainer->id;
                $lastmod = $trainer->updated_at ?? $trainer->created_at ?? now();
                $xml .= $this->urlEntry($path, '0.7', 'monthly', $lastmod, $baseUrl);
            }
        } catch (\Exception $e) {
            // Continue without trainers if DB unavailable
        }
        
        $xml .= '</urlset>';
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Generate URL entry with hreflang tags
     */
    private function urlEntry(string $path, string $priority, string $changefreq, $lastmod, string $baseUrl): string
    {
        $path = '/' . ltrim($path, '/');
        
        if (!$lastmod instanceof \Illuminate\Support\Carbon) {
            $lastmod = now();
        }
        
        // Canonical URL (Hebrew)
        $canonical = $baseUrl . '/he' . $path;
        
        $xml = '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($canonical, ENT_XML1, 'UTF-8') . '</loc>' . "\n";
        $xml .= '    <lastmod>' . $lastmod->toAtomString() . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . htmlspecialchars($changefreq, ENT_XML1, 'UTF-8') . '</changefreq>' . "\n";
        $xml .= '    <priority>' . htmlspecialchars($priority, ENT_XML1, 'UTF-8') . '</priority>' . "\n";
        
        // Hreflang tags for all languages
        foreach (self::LOCALES as $locale) {
            $url = $baseUrl . '/' . $locale . $path;
            $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($locale, ENT_XML1, 'UTF-8') . '" href="' . htmlspecialchars($url, ENT_XML1, 'UTF-8') . '" />' . "\n";
        }
        
        // Backward-compatible URL (without prefix)
        $backwardUrl = $baseUrl . $path;
        if ($backwardUrl !== $canonical) {
            $xml .= '    <xhtml:link rel="alternate" hreflang="he" href="' . htmlspecialchars($backwardUrl, ENT_XML1, 'UTF-8') . '" />' . "\n";
        }
        
        // x-default
        $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($canonical, ENT_XML1, 'UTF-8') . '" />' . "\n";
        
        $xml .= '  </url>' . "\n";
        
        return $xml;
    }
}
