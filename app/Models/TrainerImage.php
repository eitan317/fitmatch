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
        
        if (file_exists(storage_path('app/public/' . $thumbnailPath))) {
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
            $url = \Storage::url($this->thumbnail_path);
            if (!str_starts_with($url, 'http')) {
                $url = url($url);
            }
            return $url;
        } catch (\Exception $e) {
            return url('/storage/' . $this->thumbnail_path);
        }
    }
}

