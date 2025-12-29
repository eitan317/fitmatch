<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\TrainerImage;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TrainerController extends Controller
{
    /**
     * Display a listing of all trainers with new status system.
     */
    public function index()
    {
        // Platform Statistics
        $totalReviews = \App\Models\Review::count();
        // Calculate trial and active trainers separately for display
        $trialTrainersCount = Trainer::where('status', 'trial')->where('approved_by_admin', true)->count();
        $activeTrainersCount = Trainer::where('status', 'active')->where('approved_by_admin', true)->count();
        
        // Total visible trainers = active + trial (this matches what users see on trainers page)
        $totalVisibleTrainers = $trialTrainersCount + $activeTrainersCount;
        
        $stats = [
            'total_users' => \App\Models\User::count(),
            'total_trainers' => Trainer::count(),
            'trial_trainers' => $trialTrainersCount,
            'pending_payment_trainers' => Trainer::where('status', 'pending_payment')->count(),
            'active_trainers' => $activeTrainersCount,
            'total_visible_trainers' => $totalVisibleTrainers, // Active + Trial combined (what users see)
            'blocked_trainers' => Trainer::where('status', 'blocked')->count(),
            'pending_trainers' => Trainer::where('status', 'pending')->count(),
            'total_reviews' => $totalReviews,
            'average_rating' => $totalReviews > 0 ? round(\App\Models\Review::avg('rating'), 1) : 0,
            'trainers_this_month' => Trainer::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'trainers_last_7_days' => Trainer::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        // Add page views statistics if PageView model exists
        if (\Illuminate\Support\Facades\Schema::hasTable('page_views')) {
            $stats['total_page_views'] = \App\Models\PageView::count();
            $stats['page_views_today'] = \App\Models\PageView::whereDate('viewed_at', today())->count();
            $stats['page_views_this_month'] = \App\Models\PageView::whereMonth('viewed_at', now()->month)
                ->whereYear('viewed_at', now()->year)
                ->count();
        } else {
            $stats['total_page_views'] = 0;
            $stats['page_views_today'] = 0;
            $stats['page_views_this_month'] = 0;
        }

        // Get trainers by status - only approved trainers
        $trialTrainers = Trainer::where('status', 'trial')
            ->where('approved_by_admin', true)
            ->with('profileImage')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingPaymentTrainers = Trainer::where('status', 'pending_payment')
            ->with('profileImage')
            ->orderBy('created_at', 'desc')
            ->get();

        $activeTrainers = Trainer::where('status', 'active')
            ->where('approved_by_admin', true)
            ->with(['reviews', 'profileImage'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingTrainers = Trainer::where('status', 'pending')
            ->with('profileImage')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all trainers (for admin to see everything)
        $allTrainers = Trainer::orderBy('created_at', 'desc')
            ->with(['reviews', 'profileImage'])
            ->get();

        return view('admin', compact('stats', 'trialTrainers', 'pendingPaymentTrainers', 'activeTrainers', 'pendingTrainers', 'allTrainers'));
    }

    /**
     * Approve the specified trainer.
     */
    public function approve(Trainer $trainer)
    {
        $updateData = [
            'approved_by_admin' => true,
            'status' => 'active', // כל מאמן מאושר הופך לפעיל
            'last_payment_at' => now(),
        ];
        
        // אם המאמן בחר trial, נשמור את המידע אבל הסטטוס יהיה active
        if ($trainer->plan_choice === 'trial') {
            $updateData['trial_started_at'] = now();
            $updateData['trial_ends_at'] = now()->addDays(30);
        }
        
        $trainer->update($updateData);

        return redirect()->route('admin.trainers.index')
            ->with('success', 'המאמן אושר והופעל בהצלחה.');
    }

    /**
     * Approve payment and activate trainer (for pending_payment status).
     */
    public function approvePayment(Trainer $trainer)
    {
        if ($trainer->status !== 'pending_payment') {
            return redirect()->route('admin.trainers.index')
                ->with('error', 'ניתן לאשר תשלום רק למאמנים בסטטוס pending_payment.');
        }

        $trainer->update([
            'status' => 'active',
            'approved_by_admin' => true,
            'last_payment_at' => now(),
        ]);

        return redirect()->route('admin.trainers.index')
            ->with('success', 'התשלום אושר והמאמן הופעל בהצלחה.');
    }

    /**
     * Reject the specified trainer.
     */
    public function reject(Trainer $trainer)
    {
        try {
            $trainerName = $trainer->full_name;
            $trainerId = $trainer->id;

            // Use DB transaction to ensure all deletions succeed
            DB::transaction(function () use ($trainer, $trainerId) {
                // Force delete related reviews first (use forceDelete to ensure complete removal)
                $trainer->reviews()->each(function ($review) {
                    $review->forceDelete();
                });

                // Also try direct DB delete as backup
                DB::table('reviews')->where('trainer_id', $trainerId)->delete();

                // Force delete the trainer using DB statement to bypass any constraints
                DB::table('trainers')->where('id', $trainerId)->delete();
            });

            \Log::info("Trainer rejected and deleted successfully: ID {$trainerId}, Name: {$trainerName}");

            return redirect()->route('admin.trainers.index')
                ->with('success', 'המאמן נדחה ונמחק.');
        } catch (\Exception $e) {
            \Log::error('Error rejecting trainer: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('admin.trainers.index')
                ->with('error', 'אירעה שגיאה בדחיית המאמן. אנא נסה שוב.');
        }
    }

    /**
     * Delete an approved trainer from the system.
     */
    public function destroy(Trainer $trainer)
    {
        try {
            $trainerName = $trainer->full_name;
            $trainerId = $trainer->id;

            // Use DB transaction to ensure all deletions succeed
            DB::transaction(function () use ($trainer, $trainerId) {
                // Force delete related reviews first (use forceDelete to ensure complete removal)
                $trainer->reviews()->each(function ($review) {
                    $review->forceDelete();
                });

                // Also try direct DB delete as backup
                DB::table('reviews')->where('trainer_id', $trainerId)->delete();

                // Force delete the trainer using DB statement to bypass any constraints
                DB::table('trainers')->where('id', $trainerId)->delete();
            });

            \Log::info("Trainer deleted successfully: ID {$trainerId}, Name: {$trainerName}");

            return redirect()->route('admin.trainers.index')
                ->with('success', 'המאמן "' . $trainerName . '" נמחק בהצלחה מהמערכת.');
        } catch (\Exception $e) {
            \Log::error('Error deleting trainer: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->route('admin.trainers.index')
                ->with('error', 'אירעה שגיאה במחיקת המאמן: ' . $e->getMessage());
        }
    }

    /**
     * Delete all active/trial trainers (cleanup).
     */
    public function cleanupAll()
    {
        try {
            $trainers = Trainer::whereIn('status', ['active', 'trial'])->get();
            $count = $trainers->count();
            
            if ($count === 0) {
                return redirect()->route('admin.trainers.index')
                    ->with('info', 'לא נמצאו מאמנים למחיקה.');
            }

            $ids = $trainers->pluck('id')->toArray();

            DB::transaction(function () use ($ids) {
                DB::table('reviews')->whereIn('trainer_id', $ids)->delete();
                DB::table('trainers')->whereIn('id', $ids)->delete();
            });

            \Log::info("Deleted {$count} trainers in cleanup");

            return redirect()->route('admin.trainers.index')
                ->with('success', "נמחקו {$count} מאמנים בהצלחה מהמערכת.");
        } catch (\Exception $e) {
            \Log::error('Error in cleanup: ' . $e->getMessage());
            return redirect()->route('admin.trainers.index')
                ->with('error', 'אירעה שגיאה במחיקה. אנא נסה שוב.');
        }
    }

    /**
     * Show the form for editing the specified trainer.
     */
    public function edit(Trainer $trainer)
    {
        return view('edit-trainer', compact('trainer'));
    }

    /**
     * Update the specified trainer in storage.
     */
    public function update(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'age' => 'nullable|integer|min:18|max:120',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'experience_years' => 'nullable|integer|min:0',
            'main_specialization' => 'nullable|string|max:255',
            'price_per_session' => 'nullable|numeric|min:0',
            'training_types' => 'nullable|array',
            'instagram' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ], [
            'age.min' => 'הגיל המינימלי המותר הוא 18',
            'age.integer' => 'הגיל חייב להיות מספר שלם',
        ]);

        // Handle subscription plan change
        if ($request->has('subscription_plan_id')) {
            $validated['subscription_plan_id'] = $request->subscription_plan_id ?: null;
        }

        // Validate training types count based on subscription plan
        if ($validated['subscription_plan_id'] ?? $trainer->subscription_plan_id) {
            $planId = $validated['subscription_plan_id'] ?? $trainer->subscription_plan_id;
            $plan = SubscriptionPlan::find($planId);
            if ($plan && $plan->max_training_types !== null) {
                $trainingTypesCount = count($validated['training_types'] ?? []);
                if ($trainingTypesCount > $plan->max_training_types) {
                    return redirect()->back()
                        ->withErrors(['training_types' => "תכנית המנוי מאפשרת עד {$plan->max_training_types} סוגי אימונים בלבד."])
                        ->withInput();
                }
            }
        }

        $trainer->update($validated);

        // Handle new image upload
        if ($request->hasFile('new_image')) {
            $file = $request->file('new_image');
            if ($file && $file->getSize() > 0) {
                try {
                    $originalExtension = $file->getClientOriginalExtension();
                    if (empty($originalExtension)) {
                        $mimeType = $file->getMimeType();
                        $extensionMap = [
                            'image/jpeg' => 'jpg',
                            'image/png' => 'png',
                            'image/gif' => 'gif',
                            'image/webp' => 'webp',
                            'image/bmp' => 'bmp',
                        ];
                        $originalExtension = $extensionMap[$mimeType] ?? 'jpg';
                    }
                    
                    $filename = time() . '_' . uniqid() . '.' . $originalExtension;
                    $imagePath = $file->storeAs('trainer-images', $filename, 'public');
                    $fullPath = storage_path('app/public/' . $imagePath);
                    
                    if ($imagePath && file_exists($fullPath)) {
                        // Resize image if Intervention Image is available
                        if (class_exists(\Intervention\Image\ImageManager::class)) {
                            try {
                                $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                                
                                $image = $manager->read($fullPath);
                                $image->scale(width: 1000, height: 1000);
                                $image->save($fullPath, quality: 90);
                                
                                // Create thumbnail
                                $thumbnailDir = storage_path('app/public/trainer-images/thumbnails');
                                if (!File::exists($thumbnailDir)) {
                                    File::makeDirectory($thumbnailDir, 0755, true);
                                }
                                $thumbnailPath = storage_path('app/public/trainer-images/thumbnails/' . $filename);
                                $thumbnail = $manager->read($fullPath);
                                $thumbnail->cover(200, 200);
                                $thumbnail->save($thumbnailPath, quality: 85);
                            } catch (\Exception $e) {
                                \Log::warning('Error resizing image in admin update: ' . $e->getMessage());
                            }
                        }
                        
                        TrainerImage::create([
                            'trainer_id' => $trainer->id,
                            'image_path' => $imagePath,
                            'image_type' => 'profile',
                            'sort_order' => 0,
                            'is_primary' => false,
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error uploading image in admin update: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.trainers.index')
            ->with('success', 'המאמן עודכן בהצלחה.');
    }

    /**
     * Block a trainer.
     */
    public function block(Trainer $trainer)
    {
        $trainer->update(['status' => 'blocked']);

        return redirect()->back()
            ->with('success', 'המאמן נחסם בהצלחה.');
    }

    /**
     * Unblock a trainer.
     */
    public function unblock(Trainer $trainer)
    {
        // Restore to pending if was blocked, or keep current status
        if ($trainer->status === 'blocked') {
            $trainer->update(['status' => 'pending']);
        }

        return redirect()->back()
            ->with('success', 'המאמן שוחרר בהצלחה.');
    }

    /**
     * Change trainer subscription plan.
     */
    public function changeSubscription(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
        ]);

        $trainer->update($validated);

        $planName = $trainer->subscriptionPlan ? $trainer->subscriptionPlan->name : 'ללא מנוי';
        
        return redirect()->back()
            ->with('success', "מנוי המאמן עודכן ל: {$planName}.");
    }

    /**
     * Delete a trainer image.
     */
    public function deleteImage(Trainer $trainer, TrainerImage $image)
    {
        // Verify the image belongs to this trainer
        if ($image->trainer_id !== $trainer->id) {
            abort(403, 'Unauthorized');
        }

        // Delete the image file
        try {
            if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            // Delete thumbnail if exists
            if ($image->thumbnail_path && Storage::disk('public')->exists($image->thumbnail_path)) {
                Storage::disk('public')->delete($image->thumbnail_path);
            }
        } catch (\Exception $e) {
            \Log::warning('Error deleting image file: ' . $e->getMessage());
        }

        // Delete the database record
        $image->delete();

        return redirect()->back()
            ->with('success', 'התמונה נמחקה בהצלחה.');
    }

    /**
     * Set an image as primary.
     */
    public function setPrimaryImage(Trainer $trainer, TrainerImage $image)
    {
        // Verify the image belongs to this trainer
        if ($image->trainer_id !== $trainer->id) {
            abort(403, 'Unauthorized');
        }

        // Remove primary from all other images
        TrainerImage::where('trainer_id', $trainer->id)
            ->where('id', '!=', $image->id)
            ->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return redirect()->back()
            ->with('success', 'התמונה הוגדרה כתמונה ראשית.');
    }
}

