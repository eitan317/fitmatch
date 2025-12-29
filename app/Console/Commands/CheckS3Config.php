<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckS3Config extends Command
{
    protected $signature = 'trainers:check-s3-config';
    protected $description = 'Check S3 configuration';

    public function handle()
    {
        $this->info("Checking S3 Configuration...\n");
        
        $filesystemDisk = env('FILESYSTEM_DISK', 'local');
        $this->info("FILESYSTEM_DISK: {$filesystemDisk}");
        
        if ($filesystemDisk === 's3') {
            $this->info("\nS3 Configuration:");
            $this->info("  AWS_ACCESS_KEY_ID: " . (env('AWS_ACCESS_KEY_ID') ? (substr(env('AWS_ACCESS_KEY_ID'), 0, 10) . '...') : 'NOT SET'));
            $this->info("  AWS_SECRET_ACCESS_KEY: " . (env('AWS_SECRET_ACCESS_KEY') ? '***SET***' : 'NOT SET'));
            $this->info("  AWS_DEFAULT_REGION: " . (env('AWS_DEFAULT_REGION') ?: 'NOT SET'));
            $this->info("  AWS_BUCKET: " . (env('AWS_BUCKET') ?: 'NOT SET'));
            $this->info("  AWS_URL: " . (env('AWS_URL') ?: 'NOT SET (will be auto-generated)'));
            
            // Check if credentials are placeholder values
            $accessKey = env('AWS_ACCESS_KEY_ID');
            if ($accessKey && (str_contains($accessKey, 'your_access_key') || str_contains($accessKey, 'placeholder'))) {
                $this->error("\n⚠️  WARNING: AWS_ACCESS_KEY_ID appears to be a placeholder value!");
                $this->error("   Please set the actual AWS Access Key ID in Railway variables.");
            }
            
            if (empty($accessKey)) {
                $this->error("\n⚠️  ERROR: AWS_ACCESS_KEY_ID is not set!");
                $this->error("   Please set it in Railway variables.");
            }
            
            $secretKey = env('AWS_SECRET_ACCESS_KEY');
            if (empty($secretKey)) {
                $this->error("\n⚠️  ERROR: AWS_SECRET_ACCESS_KEY is not set!");
                $this->error("   Please set it in Railway variables.");
            }
            
            $bucket = env('AWS_BUCKET');
            if (empty($bucket)) {
                $this->error("\n⚠️  ERROR: AWS_BUCKET is not set!");
                $this->error("   Please set it in Railway variables.");
            }
        } else {
            $this->info("\nUsing local storage (not S3)");
        }
        
        $this->newLine();
        $this->info("Config from filesystems.php:");
        $publicDisk = config('filesystems.disks.public');
        $this->info("  Driver: " . ($publicDisk['driver'] ?? 'unknown'));
        $this->info("  Root: " . ($publicDisk['root'] ?? 'unknown'));
        $this->info("  Bucket: " . ($publicDisk['bucket'] ?? 'N/A'));
    }
}

