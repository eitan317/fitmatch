<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageHelper
{
    /**
     * Process and resize an uploaded image.
     * Works with both local storage and S3.
     * 
     * @param string $imagePath The path to the image in storage (e.g., 'trainer-images/filename.jpg')
     * @param string $disk The storage disk to use ('public' for local or S3)
     * @param int $maxWidth Maximum width for main image
     * @param int $maxHeight Maximum height for main image
     * @param int $thumbnailWidth Thumbnail width
     * @param int $thumbnailHeight Thumbnail height
     * @return array Returns ['main' => bool, 'thumbnail' => bool] indicating success
     */
    public static function processImage(
        string $imagePath,
        string $disk = 'public',
        int $maxWidth = 1000,
        int $maxHeight = 1000,
        int $thumbnailWidth = 200,
        int $thumbnailHeight = 200
    ): array {
        $result = ['main' => false, 'thumbnail' => false];
        
        if (!Storage::disk($disk)->exists($imagePath)) {
            \Log::warning("Image not found in storage: {$imagePath}");
            return $result;
        }

        try {
            // Try to create ImageManager with available driver
            $manager = null;
            
            // Try Imagick first (usually more reliable on servers)
            if (extension_loaded('imagick') && class_exists('Imagick')) {
                try {
                    $manager = new ImageManager(new \Intervention\Image\Drivers\Imagick\Driver());
                } catch (\Exception $e) {
                    \Log::warning('Imagick driver failed, trying GD: ' . $e->getMessage());
                }
            }
            
            // Fallback to GD if Imagick not available
            if (!$manager && extension_loaded('gd')) {
                try {
                    $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                } catch (\Exception $e) {
                    \Log::warning('GD driver failed: ' . $e->getMessage());
                }
            }
            
            if (!$manager) {
                \Log::warning('No image driver available (GD or Imagick). Image saved without resizing.');
                return $result;
            }

            // Get file content from storage (works with both local and S3)
            $imageContent = Storage::disk($disk)->get($imagePath);
            
            // Create temporary file for processing
            $tempFile = tempnam(sys_get_temp_dir(), 'img_');
            if (!$tempFile) {
                \Log::error('Failed to create temporary file for image processing');
                return $result;
            }
            
            // Write content to temp file
            file_put_contents($tempFile, $imageContent);
            
            try {
                // Process main image
                $image = $manager->read($tempFile);
                $image->scale(width: $maxWidth, height: $maxHeight);
                
                // Save to temp file
                $tempProcessed = tempnam(sys_get_temp_dir(), 'img_processed_');
                $image->save($tempProcessed, quality: 90);
                
                // Upload processed image back to storage
                $processedContent = file_get_contents($tempProcessed);
                Storage::disk($disk)->put($imagePath, $processedContent);
                $result['main'] = true;
                
                // Clean up temp processed file
                @unlink($tempProcessed);
                
                // Create thumbnail
                $filename = basename($imagePath);
                $thumbnailPath = 'trainer-images/thumbnails/' . $filename;
                
                // Read original temp file again for thumbnail
                $thumbnail = $manager->read($tempFile);
                $thumbnail->cover($thumbnailWidth, $thumbnailHeight);
                
                // Save thumbnail to temp file
                $tempThumbnail = tempnam(sys_get_temp_dir(), 'img_thumb_');
                $thumbnail->save($tempThumbnail, quality: 85);
                
                // Upload thumbnail to storage
                $thumbnailContent = file_get_contents($tempThumbnail);
                Storage::disk($disk)->put($thumbnailPath, $thumbnailContent);
                $result['thumbnail'] = true;
                
                // Clean up temp thumbnail file
                @unlink($tempThumbnail);
                
            } finally {
                // Always clean up original temp file
                @unlink($tempFile);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error processing image: ' . $e->getMessage());
        }
        
        return $result;
    }
    
    /**
     * Check if a file exists in storage (works with both local and S3).
     */
    public static function fileExists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }
    
    /**
     * Get the full path to a file (for local storage) or URL (for S3).
     * Returns null if file doesn't exist.
     */
    public static function getFilePath(string $path, string $disk = 'public'): ?string
    {
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }
        
        // For local storage, return full filesystem path
        if ($disk === 'public' && config('filesystems.disks.public.driver') === 'local') {
            return storage_path('app/public/' . $path);
        }
        
        // For S3 or other cloud storage, return URL
        return Storage::disk($disk)->url($path);
    }
}

