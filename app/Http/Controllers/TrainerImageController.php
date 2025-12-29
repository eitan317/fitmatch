<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\TrainerImage;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

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
            
            // Use 'public' disk (which can be configured as S3 or local)
            $disk = 'public';
            
            // Save file to storage (works with both local and S3)
            try {
                $imagePath = $file->storeAs('trainer-images', $filename, $disk);
            } catch (\Exception $storageException) {
                \Log::error('TrainerImageController::store - Failed to save file to storage', [
                    'trainer_id' => $trainer->id,
                    'filename' => $filename,
                    'disk' => $disk,
                    'error' => $storageException->getMessage(),
                    'trace' => $storageException->getTraceAsString(),
                ]);
                return redirect()->back()
                    ->with('error', 'שגיאה בשמירת התמונה: ' . $storageException->getMessage());
            }
            
            if (!$imagePath) {
                \Log::error('TrainerImageController::store - No path returned from storeAs', [
                    'trainer_id' => $trainer->id,
                    'filename' => $filename,
                    'disk' => $disk,
                ]);
                return redirect()->back()
                    ->with('error', 'שגיאה בשמירת התמונה - לא התקבל נתיב קובץ.');
            }
            
            // Try to verify file exists, but don't fail if check fails (S3 might have credential issues)
            $fileExists = false;
            try {
                $fileExists = Storage::disk($disk)->exists($imagePath);
            } catch (\Exception $checkException) {
                \Log::warning('TrainerImageController::store - Could not verify file existence', [
                    'trainer_id' => $trainer->id,
                    'image_path' => $imagePath,
                    'error' => $checkException->getMessage(),
                ]);
                // Continue anyway - assume file was saved if we got a path
                $fileExists = true;
            }
            
            if (!$fileExists) {
                \Log::error('TrainerImageController::store - File path returned but file does not exist', [
                    'trainer_id' => $trainer->id,
                    'image_path' => $imagePath,
                    'disk' => $disk,
                ]);
                return redirect()->back()
                    ->with('error', 'שגיאה בשמירת התמונה - הקובץ לא נמצא.');
            }

            // Process image (resize and create thumbnail) - works with both local and S3
            try {
                ImageHelper::processImage($imagePath, $disk);
            } catch (\Exception $e) {
                \Log::warning('Error processing image: ' . $e->getMessage());
                // Continue even if processing fails - image is already saved
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
            // Use 'public' disk (which can be configured as S3 or local)
            $disk = 'public';
            
            // Delete file from storage (works with both local and S3)
            if ($image->image_path && Storage::disk($disk)->exists($image->image_path)) {
                Storage::disk($disk)->delete($image->image_path);
            }
            
            // Delete thumbnail if exists
            $filename = basename($image->image_path);
            $thumbnailPath = 'trainer-images/thumbnails/' . $filename;
            if (Storage::disk($disk)->exists($thumbnailPath)) {
                Storage::disk($disk)->delete($thumbnailPath);
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

