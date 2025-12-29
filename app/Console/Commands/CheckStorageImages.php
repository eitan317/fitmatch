<?php

namespace App\Console\Commands;

use App\Models\Trainer;
use App\Models\TrainerImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckStorageImages extends Command
{
    protected $signature = 'trainers:check-storage';
    protected $description = 'Check if there are images in storage that are not in database';

    public function handle()
    {
        $disk = Storage::disk('public');
        $driver = config('filesystems.disks.public.driver');
        
        $this->info("Storage Driver: {$driver}");
        $this->info("Checking storage for images...\n");
        
        // Get all files in trainer-images directory
        $files = $disk->files('trainer-images');
        
        // Filter out thumbnails
        $imageFiles = array_filter($files, function($file) {
            return !str_contains($file, 'thumbnails/');
        });
        
        $this->info("Found " . count($imageFiles) . " image file(s) in storage:\n");
        
        foreach ($imageFiles as $file) {
            $this->line("  - {$file}");
            
            // Check if this file exists in database
            $existsInDb = TrainerImage::where('image_path', $file)->exists();
            
            if ($existsInDb) {
                $this->info("    ✓ Exists in database");
            } else {
                $this->warn("    ✗ NOT in database!");
                
                // Try to find which trainer this might belong to by filename
                // Filename format: timestamp_uniqid.ext
                // We can't reliably match, but we can show the file
            }
        }
        
        $this->newLine();
        $this->info("Checking database records...\n");
        
        $dbImages = TrainerImage::all();
        $this->info("Found " . $dbImages->count() . " image record(s) in database:\n");
        
        foreach ($dbImages as $image) {
            $exists = $disk->exists($image->image_path);
            
            if ($exists) {
                $this->info("  ✓ Image ID {$image->id}: {$image->image_path} (Trainer ID: {$image->trainer_id})");
            } else {
                $this->error("  ✗ Image ID {$image->id}: {$image->image_path} - FILE NOT FOUND! (Trainer ID: {$image->trainer_id})");
            }
        }
        
        $this->newLine();
        $this->info("Done!");
    }
}

