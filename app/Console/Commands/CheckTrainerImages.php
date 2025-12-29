<?php

namespace App\Console\Commands;

use App\Models\Trainer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckTrainerImages extends Command
{
    protected $signature = 'trainers:check-images {trainer_id?}';
    protected $description = 'Check trainer images and URLs';

    public function handle()
    {
        $trainerId = $this->argument('trainer_id');
        
        if ($trainerId) {
            $trainers = Trainer::where('id', $trainerId)->with('profileImage')->get();
        } else {
            $trainers = Trainer::with('profileImage')->get();
        }
        
        $this->info("Checking " . $trainers->count() . " trainer(s)...\n");
        
        foreach ($trainers as $trainer) {
            $this->info("Trainer: {$trainer->full_name} (ID: {$trainer->id})");
            
            $profileImage = $trainer->profileImage;
            if ($profileImage) {
                $this->info("  Profile Image ID: {$profileImage->id}");
                $this->info("  Image Path: {$profileImage->image_path}");
                $this->info("  Image Type: {$profileImage->image_type}");
                $this->info("  Is Primary: " . ($profileImage->is_primary ? 'Yes' : 'No'));
                
                $exists = Storage::disk('public')->exists($profileImage->image_path);
                $this->info("  File Exists: " . ($exists ? 'Yes' : 'No'));
                
                if (!$exists) {
                    $this->error("  ⚠️  File does NOT exist in storage!");
                }
                
                $url = $profileImage->image_url;
                $this->info("  Image URL: {$url}");
                
                // Check all images for this trainer
                $allImages = $trainer->images;
                $this->info("  Total Images: " . $allImages->count());
                foreach ($allImages as $img) {
                    $this->line("    - Image ID: {$img->id}, Path: {$img->image_path}, Primary: " . ($img->is_primary ? 'Yes' : 'No'));
                }
            } else {
                $this->warn("  ❌ No profile image found!");
                
                // Check if there are any images at all
                $allImages = $trainer->images;
                if ($allImages->count() > 0) {
                    $this->warn("  ⚠️  But there are " . $allImages->count() . " image(s) in database:");
                    foreach ($allImages as $img) {
                        $this->line("    - Image ID: {$img->id}, Path: {$img->image_path}, Type: {$img->image_type}, Primary: " . ($img->is_primary ? 'Yes' : 'No'));
                    }
                } else {
                    $this->warn("  ⚠️  No images at all in database for this trainer!");
                }
            }
            
            $this->newLine();
        }
        
        $this->info("Done! Check storage/logs/laravel.log for detailed information.");
    }
}

