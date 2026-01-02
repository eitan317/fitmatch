<?php

namespace App\Console\Commands;

use App\Models\Trainer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--force : Force regenerate even if file exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml file in public directory with all static pages and trainer profiles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemapPath = public_path('sitemap.xml');
        
        // Check if file exists and ask for confirmation unless --force
        if (File::exists($sitemapPath) && !$this->option('force')) {
            if (!$this->confirm('Sitemap file already exists. Do you want to regenerate it?')) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $this->info('Generating sitemap.xml...');

        $baseUrl = config('app.url');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Homepage
        $xml .= $this->formatUrl($baseUrl . '/', '1.0', 'daily', now());
        
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
            $xml .= $this->formatUrl($baseUrl . $page, '0.8', 'weekly', now()->subDays(7));
        }
        
        // Get all approved trainers
        $query = Trainer::where('approved_by_admin', true);
        
        if (Schema::hasColumn('trainers', 'status')) {
            $query->whereIn('status', ['active', 'trial']);
        }
        
        $trainers = $query->get();
        
        $this->info("Found {$trainers->count()} approved trainers to include in sitemap.");
        
        foreach ($trainers as $trainer) {
            $url = $baseUrl . '/trainers/' . $trainer->id;
            $lastmod = $trainer->updated_at ?? $trainer->created_at ?? now();
            $xml .= $this->formatUrl($url, '0.7', 'monthly', $lastmod);
        }
        
        $xml .= '</urlset>';
        
        // Write to file
        try {
            File::put($sitemapPath, $xml);
            $this->info("Sitemap generated successfully at: {$sitemapPath}");
            $this->info("Total URLs: " . (count($pages) + 1 + $trainers->count()));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to generate sitemap: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Format a URL entry for the sitemap.
     *
     * @param string $loc
     * @param string $priority
     * @param string $changefreq
     * @param \Illuminate\Support\Carbon|\DateTime|string $lastmod
     * @return string
     */
    private function formatUrl($loc, $priority, $changefreq, $lastmod)
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

