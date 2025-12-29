<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerImage extends Model
{
    protected $fillable = [
        'trainer_id',
        'image_path',
        'image_type',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the trainer that owns the image.
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * Get the thumbnail path for this image.
     */
    public function getThumbnailPathAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }
        
        $filename = basename($this->image_path);
        $thumbnailPath = 'trainer-images/thumbnails/' . $filename;
        
        // Check if thumbnail exists in storage (works with both local and S3)
        // Use 'public' disk (which can be configured as S3 or local)
        if (\Storage::disk('public')->exists($thumbnailPath)) {
            return $thumbnailPath;
        }
        
        return null;
    }

    /**
     * Get the full URL for the thumbnail.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }
        
        try {
            // Use 'public' disk (works with both S3 and local)
            $url = \Storage::disk('public')->url($this->thumbnail_path);
            
            // If URL doesn't start with http, make it absolute
            if (!str_starts_with($url, 'http')) {
                $url = url($url);
            }
            
            return $url;
        } catch (\Exception $e) {
            // Fallback: try direct URL
            return url('/storage/' . $this->thumbnail_path);
        }
    }

    /**
     * Get the full URL for the image.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            \Log::warning('TrainerImage: No image_path', [
                'trainer_image_id' => $this->id,
                'trainer_id' => $this->trainer_id,
            ]);
            return null;
        }
        
        try {
            $disk = \Storage::disk('public');
            $driver = config('filesystems.disks.public.driver');
            
            // Check if file exists
            $exists = $disk->exists($this->image_path);
            
            \Log::info('TrainerImage URL generation', [
                'image_id' => $this->id,
                'image_path' => $this->image_path,
                'disk_driver' => $driver,
                'file_exists' => $exists,
            ]);
            
            if (!$exists) {
                \Log::warning('TrainerImage: File does not exist in storage', [
                    'image_path' => $this->image_path,
                    'disk' => 'public',
                ]);
            }
            
            $url = $disk->url($this->image_path);
            
            \Log::info('TrainerImage: Generated URL', [
                'image_path' => $this->image_path,
                'raw_url' => $url,
            ]);
            
            if (!str_starts_with($url, 'http')) {
                if (str_starts_with($url, '/storage')) {
                    $url = url($url);
                } else {
                    $url = url('/storage/' . ltrim($url, '/'));
                }
            }
            
            \Log::info('TrainerImage: Final URL', [
                'image_path' => $this->image_path,
                'final_url' => $url,
            ]);
            
            return $url;
        } catch (\Exception $e) {
            \Log::error('TrainerImage: Error generating URL', [
                'image_path' => $this->image_path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return url('/storage/' . $this->image_path);
        }
    }
}

