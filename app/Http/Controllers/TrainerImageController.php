<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\TrainerImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;

class TrainerImageController extends Controller
{
    /**
     * Store a newly uploaded image for a trainer.
     */
    public function store(Request $request, Trainer $trainer)
    {
        // Verify the trainer belongs to the authenticated user
        if ($trainer->owner_email !== Auth::user()->email) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'image' => 'required|file', // Minimal validation - just check it's a file
            'image_type' => 'nullable|string|in:profile,gallery',
            'is_primary' => 'nullable|boolean',
        ]);

        $file = $request->file('image');
        
        if (!$file || $file->getSize() <= 0) {
            return redirect()->back()
                ->with('error', 'הקובץ לא תקין.');
        }

        try {
            // Generate unique filename
            $originalExtension = $file->getClientOriginalExtension();
            if (empty($originalExtension)) {
                $mimeType = $file->getMimeType();
                $extensionMap = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                    'image/bmp' => 'bmp',
                    'image/svg+xml' => 'svg',
                ];
                $originalExtension = $extensionMap[$mimeType] ?? 'jpg';
            }
            
            $filename = time() . '_' . uniqid() . '.' . $originalExtension;
            
            // Ensure directory exists
            $trainerImagesPath = storage_path('app/public/trainer-images');
            if (!File::exists($trainerImagesPath)) {
                File::makeDirectory($trainerImagesPath, 0755, true);
            }
            
            // Save file
            $imagePath = $file->storeAs('trainer-images', $filename, 'public');
            $fullPath = storage_path('app/public/' . $imagePath);
            
            if (!$imagePath || !file_exists($fullPath)) {
                return redirect()->back()
                    ->with('error', 'שגיאה בשמירת התמונה.');
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
                
                // Only resize if we have a working manager
                if ($manager) {
                    // Resize main image to max 1000x1000px
                    $image = $manager->read($fullPath);
                    $image->scale(width: 1000, height: 1000);
                    $image->save($fullPath, quality: 90);
                    
                    // Create thumbnail directory if needed
                    $thumbnailDir = storage_path('app/public/trainer-images/thumbnails');
                    if (!File::exists($thumbnailDir)) {
                        File::makeDirectory($thumbnailDir, 0755, true);
                    }
                    
                    // Create thumbnail 200x200px
                    $thumbnailPath = storage_path('app/public/trainer-images/thumbnails/' . $filename);
                    $thumbnail = $manager->read($fullPath);
                    $thumbnail->cover(200, 200);
                    $thumbnail->save($thumbnailPath, quality: 85);
                } else {
                    \Log::warning('No image driver available (GD or Imagick). Image saved without resizing.');
                }
            } catch (\Exception $e) {
                \Log::warning('Error resizing image: ' . $e->getMessage());
                // Continue even if resize fails - image is already saved
            }

            // If this is set as primary, remove primary from other images
            $isPrimary = $request->has('is_primary') && $request->is_primary;
            if ($isPrimary) {
                TrainerImage::where('trainer_id', $trainer->id)
                    ->where('is_primary', true)
                    ->update(['is_primary' => false]);
            }

            // Create database record
            $trainerImage = TrainerImage::create([
                'trainer_id' => $trainer->id,
                'image_path' => $imagePath,
                'image_type' => $validated['image_type'] ?? 'profile',
                'sort_order' => 0,
                'is_primary' => $isPrimary,
            ]);

            return redirect()->back()
                ->with('success', 'התמונה הועלתה בהצלחה.');
        } catch (\Exception $e) {
            \Log::error('Error uploading trainer image: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'שגיאה בהעלאת התמונה.');
        }
    }

    /**
     * Update the specified image.
     */
    public function update(Request $request, TrainerImage $image)
    {
        // Verify the image belongs to a trainer owned by the authenticated user
        if ($image->trainer->owner_email !== Auth::user()->email) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'image_type' => 'nullable|string|in:profile,gallery',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $image->update($validated);

        return redirect()->back()
            ->with('success', 'התמונה עודכנה בהצלחה.');
    }

    /**
     * Remove the specified image.
     */
    public function destroy(TrainerImage $image)
    {
        // Verify the image belongs to a trainer owned by the authenticated user
        if ($image->trainer->owner_email !== Auth::user()->email) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Delete file from storage
            $fullPath = storage_path('app/public/' . $image->image_path);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            // Delete database record
            $image->delete();

            return redirect()->back()
                ->with('success', 'התמונה נמחקה בהצלחה.');
        } catch (\Exception $e) {
            \Log::error('Error deleting trainer image: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'שגיאה במחיקת התמונה.');
        }
    }

    /**
     * Set the specified image as primary.
     */
    public function setPrimary(TrainerImage $image)
    {
        // Verify the image belongs to a trainer owned by the authenticated user
        if ($image->trainer->owner_email !== Auth::user()->email) {
            abort(403, 'Unauthorized action.');
        }

        try {
            // Remove primary from all other images of this trainer
            TrainerImage::where('trainer_id', $image->trainer_id)
                ->where('id', '!=', $image->id)
                ->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);

            return redirect()->back()
                ->with('success', 'התמונה הוגדרה כתמונת פרופיל ראשית.');
        } catch (\Exception $e) {
            \Log::error('Error setting primary image: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'שגיאה בהגדרת התמונה.');
        }
    }
}

