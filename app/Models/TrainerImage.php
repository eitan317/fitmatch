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
            return null;
        }
        
        try {
            // Use 'public' disk (works with both S3 and local)
            $disk = \Storage::disk('public');
            $url = $disk->url($this->image_path);
            
            // For S3, the URL should already be absolute (starts with http/https)
            // For local storage, it might be relative, so make it absolute
            if (!str_starts_with($url, 'http')) {
                // Check if it's a relative path (starts with /storage)
                if (str_starts_with($url, '/storage')) {
                    $url = url($url);
                } else {
                    // If it doesn't start with /, add /storage/ prefix
                    $url = url('/storage/' . ltrim($url, '/'));
                }
            }
            
            return $url;
        } catch (\Exception $e) {
            // Fallback: try direct URL
            \Log::warning('Error generating image URL: ' . $e->getMessage(), [
                'image_path' => $this->image_path,
            ]);
            return url('/storage/' . $this->image_path);
        }
    }
}

