<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function index()
    {
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
            ->header('Content-Type', 'application/xml');
    }
    
    public function main()
    {
        $baseUrl = config('app.url');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        
        // Homepage
        $xml .= $this->url($baseUrl . '/', '1.0', 'daily', now());
        
        // Static pages
        $pages = [
            '/trainers',
            '/about',
            '/faq',
            '/contact',
            '/privacy',
            '/terms',
        ];
        
        foreach ($pages as $page) {
            $xml .= $this->url($baseUrl . $page, '0.8', 'weekly', now()->subDays(7));
        }
        
        $xml .= '</urlset>';
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
    
    public function trainers()
    {
        $baseUrl = config('app.url');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Check if status column exists (for migration compatibility)
        $query = Trainer::where('approved_by_admin', true);
        
        if (\Illuminate\Support\Facades\Schema::hasColumn('trainers', 'status')) {
            $query->whereIn('status', ['active', 'trial']);
        }
        
        $trainers = $query->get();
        
        foreach ($trainers as $trainer) {
            $url = $baseUrl . '/trainers/' . $trainer->id;
            $lastmod = $trainer->updated_at ?? $trainer->created_at ?? now();
            $xml .= $this->url($url, '0.7', 'monthly', $lastmod);
        }
        
        $xml .= '</urlset>';
        
        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
    
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

