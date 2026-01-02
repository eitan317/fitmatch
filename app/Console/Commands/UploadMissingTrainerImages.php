<?php

namespace App\Console\Commands;

use App\Models\Trainer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadMissingTrainerImages extends Command
{
    protected $signature = 'trainers:upload-missing-images {--trainer-id= : Specific trainer ID} {--image-path= : Path to image file} {--auto-placeholder : Create placeholder images automatically}';
    
    protected $description = 'Upload missing trainer images. Identifies trainers with profile_image_path but missing files.';

    public function handle()
    {
        $this->info('🔍 Checking for trainers with missing images...');
        $this->newLine();

        // If specific trainer ID provided, skip the check and go directly to upload
        if ($this->option('trainer-id')) {
            $imagePath = $this->option('image-path');
            if (!$imagePath) {
                $this->error('Please provide --image-path when using --trainer-id');
                return Command::FAILURE;
            }
            return $this->handleSpecificTrainer($this->option('trainer-id'));
        }

        // Get trainers with profile_image_path but missing files
        $trainers = Trainer::whereNotNull('profile_image_path')
            ->where('profile_image_path', '!=', '')
            ->get()
            ->filter(function ($trainer) {
                $path = $trainer->profile_image_path;
                return !Storage::disk('public')->exists($path);
            });

        if ($trainers->isEmpty()) {
            $this->info('✅ No trainers with missing images found!');
            return Command::SUCCESS;
        }

        $this->warn("Found {$trainers->count()} trainer(s) with missing images:");
        $this->newLine();

        foreach ($trainers as $trainer) {
            $this->line("  - ID {$trainer->id}: {$trainer->full_name}");
            $this->line("    Missing: {$trainer->profile_image_path}");
            $this->newLine();
        }

        // If auto-placeholder option
        if ($this->option('auto-placeholder')) {
            return $this->createPlaceholders($trainers);
        }

        // Interactive mode
        $this->info("Options:");
        $this->line("  1. Upload image for specific trainer");
        $this->line("  2. Create placeholder images for all");
        $this->line("  3. Exit");
        $this->newLine();

        $choice = $this->choice('What would you like to do?', ['1', '2', '3'], '2');

        if ($choice === '1') {
            $trainerId = $this->ask('Enter trainer ID');
            $imagePath = $this->ask('Enter path to image file');
            if (!$imagePath || !file_exists($imagePath)) {
                $this->error('Invalid image path!');
                return Command::FAILURE;
            }
            return $this->handleSpecificTrainer($trainerId, $imagePath);
        } elseif ($choice === '2') {
            return $this->createPlaceholders($trainers);
        }

        return Command::SUCCESS;
    }

    protected function handleSpecificTrainer($trainerId, $imagePath = null)
    {
        $trainer = Trainer::find($trainerId);
        
        if (!$trainer) {
            $this->error("Trainer with ID {$trainerId} not found!");
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("Trainer: {$trainer->full_name} (ID: {$trainer->id})");
        if ($trainer->profile_image_path) {
            $this->info("Expected path: {$trainer->profile_image_path}");
        }
        $this->newLine();

        if (!$imagePath) {
            $imagePath = $this->option('image-path');
        }
        
        if (!$imagePath) {
            $imagePath = $this->ask('Enter path to image file');
        }

        if (!file_exists($imagePath)) {
            $this->error("File not found: {$imagePath}");
            return Command::FAILURE;
        }

        // Ensure directory exists
        $targetDir = 'trainer-images';
        Storage::disk('public')->makeDirectory($targetDir);

        // Use original filename or generate new one
        $originalFilename = basename($trainer->profile_image_path);
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION) ?: pathinfo($imagePath, PATHINFO_EXTENSION);

        // Try to use original filename first
        $targetPath = $targetDir . '/' . $originalFilename;
        
        // If file with that name exists, create new name
        if (Storage::disk('public')->exists($targetPath)) {
            $targetPath = $targetDir . '/' . time() . '_' . uniqid() . '.' . $extension;
        }

        try {
            $content = file_get_contents($imagePath);
            Storage::disk('public')->put($targetPath, $content);
            
            // Update trainer if path changed
            if ($targetPath !== $trainer->profile_image_path) {
                $trainer->profile_image_path = $targetPath;
                $trainer->save();
                $this->info("✅ Image uploaded to: {$targetPath}");
                $this->info("   (Updated trainer record)");
            } else {
                $this->info("✅ Image uploaded to: {$targetPath}");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to upload image: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function createPlaceholders($trainers)
    {
        $this->info("\n🎨 Creating placeholder images...\n");

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('trainer-images');

        foreach ($trainers as $trainer) {
            $this->line("Creating placeholder for: {$trainer->full_name} (ID: {$trainer->id})");

            try {
                $placeholderPath = $this->generatePlaceholderImage($trainer);
                
                // Update trainer record
                $trainer->profile_image_path = $placeholderPath;
                $trainer->save();
                
                $this->info("  ✅ Created: {$placeholderPath}");
            } catch (\Exception $e) {
                $this->error("  ❌ Failed: " . $e->getMessage());
            }
        }

        $this->info("\n✅ Done!");
        return Command::SUCCESS;
    }

    protected function generatePlaceholderImage($trainer)
    {
        // Get first letter of name (Hebrew support)
        $initial = mb_substr(trim($trainer->full_name), 0, 1, 'UTF-8');
        
        // Create image using GD
        $width = 400;
        $height = 400;
        $image = imagecreatetruecolor($width, $height);
        
        // Background gradient (light blue to light gray)
        $bgStart = imagecolorallocate($image, 230, 240, 250);
        $bgEnd = imagecolorallocate($image, 240, 240, 240);
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $r = (int)(230 + ($ratio * 10));
            $g = (int)(240 + ($ratio * 0));
            $b = (int)(250 - ($ratio * 10));
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $i, $width, $i, $color);
        }
        
        // Text color (primary color)
        $textColor = imagecolorallocate($image, 0, 123, 255);
        
        // Use a simple colored circle with initial
        $centerX = $width / 2;
        $centerY = $height / 2;
        $radius = 150;
        
        // Draw circle background
        $circleColor = imagecolorallocate($image, 0, 123, 255);
        imagefilledellipse($image, (int)$centerX, (int)$centerY, $radius * 2, $radius * 2, $circleColor);
        
        // Draw white text on circle (simplified - just use large font)
        $textColor = imagecolorallocate($image, 255, 255, 255);
        
        // Use largest built-in font
        $fontSize = 5; // Built-in font size
        $textBoundingBox = imagefontbbox($fontSize, 0, 'A'); // Approximate
        $textWidth = 20; // Approximate for single character
        $textHeight = 30; // Approximate
        $x = (int)(($width - $textWidth) / 2);
        $y = (int)(($height - $textHeight) / 2);
        
        // Draw initial (works for ASCII, for Hebrew we'll use a simpler approach)
        imagestring($image, $fontSize, $x, $y, $initial, $textColor);
        
        // Generate filename
        $originalPath = $trainer->profile_image_path;
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = 'placeholder_' . $trainer->id . '_' . time() . '.' . $extension;
        $path = 'trainer-images/' . $filename;
        
        // Save image
        $tempFile = tempnam(sys_get_temp_dir(), 'placeholder_');
        imagejpeg($image, $tempFile, 90);
        
        // Upload to storage
        Storage::disk('public')->put($path, file_get_contents($tempFile));
        
        // Cleanup
        imagedestroy($image);
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
        
        return $path;
    }
}

