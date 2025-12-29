<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\TrainerImage;
use App\Models\TrainerProfileView;
use App\Models\Review;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;

class TrainerController extends Controller
{
    /**
     * Display a listing of approved trainers.
     */
    public function index(Request $request)
    {
        // Show active trainers and trial trainers (trial is like a demo of paid subscription)
        // Only show trainers approved by admin
        $query = Trainer::whereIn('status', ['active', 'trial'])
            ->where('approved_by_admin', true)
            ->with(['reviews', 'subscriptionPlan', 'profileImage'])
            ->orderBy('created_at', 'desc');

        // Filter by city - case-insensitive partial match
        // MySQL with utf8mb4_unicode_ci collation is case-insensitive by default
        if ($request->has('city') && $request->city) {
            $citySearch = trim($request->city);
            $query->where('city', 'LIKE', '%' . $citySearch . '%');
        }

        // Filter by specialization
        if ($request->has('specialization') && $request->specialization) {
            $query->where('main_specialization', $request->specialization);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price_per_session', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price_per_session', '<=', $request->max_price);
        }

        // Filter by training types
        if ($request->has('training_type') && $request->training_type) {
            $query->whereJsonContains('training_types', $request->training_type);
        }

        // Search by name OR city - case-insensitive partial match
        // MySQL with utf8mb4_unicode_ci collation handles case-insensitive search automatically
        if ($request->has('search') && $request->search) {
            $searchTerm = trim($request->search);
            $query->where(function($q) use ($searchTerm) {
                // Search in full_name
                $q->where('full_name', 'LIKE', '%' . $searchTerm . '%')
                  // Also search in city field
                  ->orWhere('city', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        $trainers = $query->get();

        // Sort by subscription priority (higher priority first)
        $trainers = $trainers->sortByDesc(function ($trainer) {
            if ($trainer->subscriptionPlan) {
                return $trainer->subscriptionPlan->priority;
            }
            return 0;
        })->values();

        // Calculate average ratings
        foreach ($trainers as $trainer) {
            $trainer->average_rating = $trainer->average_rating;
            $trainer->rating_count = $trainer->rating_count;
        }

        return view('trainers', compact('trainers'));
    }

    /**
     * Show the form for creating a new trainer.
     */
    public function create()
    {
        return view('register-trainer');
    }

    /**
     * Store a newly created trainer in storage.
     */
    public function store(Request $request)
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
            'profile_image' => 'nullable|file', // Minimal validation
        ], [
            'age.min' => 'הגיל המינימלי המותר הוא 18',
            'age.integer' => 'הגיל חייב להיות מספר שלם',
        ]);

        // Note: Training types validation will be done after subscription is selected
        // This validation is handled in the subscription flow

        // Get owner email from authenticated user
        $ownerEmail = Auth::user()->email;

        $trainerData = [
            'owner_email' => $ownerEmail,
            'full_name' => $validated['full_name'],
            'age' => $validated['age'] ?? null,
            'city' => $validated['city'],
            'phone' => $validated['phone'] ?? null,
            'experience_years' => $validated['experience_years'] ?? null,
            'main_specialization' => $validated['main_specialization'] ?? null,
            'price_per_session' => $validated['price_per_session'] ?? null,
            'training_types' => $validated['training_types'] ?? [],
            'instagram' => $validated['instagram'] ?? null,
            'tiktok' => $validated['tiktok'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'status' => 'pending',
            'approved_by_admin' => false,
        ];

        // Don't include 'name' field - we use 'full_name' instead
        // If 'name' column exists and is required, it should be made nullable via migration
        // or removed from the table entirely

        $trainer = Trainer::create($trainerData);

        // Handle profile image upload if provided
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            
            if ($file && $file->getSize() > 0) {
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
                    
                    if ($imagePath && file_exists($fullPath)) {
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
                        
                        // Create database record as primary profile image
                        TrainerImage::create([
                            'trainer_id' => $trainer->id,
                            'image_path' => $imagePath,
                            'image_type' => 'profile',
                            'sort_order' => 0,
                            'is_primary' => true,
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error uploading profile image during registration: ' . $e->getMessage());
                    // Continue without image - don't fail registration
                }
            }
        }

        // Redirect to welcome page
        return redirect()->route('trainers.welcome')
            ->with('success', 'ההרשמה הושלמה בהצלחה! ברוכים הבאים!');
    }

    /**
     * Display plan choice page (pay now or trial).
     */
    public function choosePlan()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();
        
        // Check if trainer has already used trial period
        $hasUsedTrial = $trainer->trial_ends_at !== null;
        
        return view('trainer-choose-plan', compact('trainer', 'hasUsedTrial'));
    }

    /**
     * Store the trainer's plan choice (pay now or trial).
     */
    public function storePlanChoice(Request $request)
    {
        $validated = $request->validate([
            'choice' => 'required|in:pay_now,trial',
        ]);
        
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();
        
        // Security check: if already used trial, don't allow trial again
        if ($validated['choice'] === 'trial' && $trainer->trial_ends_at !== null) {
            return redirect()->back()
                ->with('error', 'לא ניתן לבחור חודש ניסיון פעם נוספת. יש לשלם כדי להמשיך.');
        }
        
        if ($validated['choice'] === 'pay_now') {
            // Chose to pay now - save choice, status remains 'pending' until admin approval
            $trainer->update([
                'plan_choice' => 'pay_now',
                // status remains 'pending'
            ]);
            
            return redirect()->route('trainers.payment-info')
                ->with('success', 'יש לשלם 20₪ דרך Bit כדי להמשיך. הבקשה ממתינה לאישור מנהל.');
        } else {
            // Chose trial period - save choice, status remains 'pending' until admin approval
            $trainer->update([
                'plan_choice' => 'trial',
                // status remains 'pending' - will be changed to 'trial' only after admin approval
            ]);
            
            return redirect()->route('trainers.trial-info')
                ->with('success', 'הבחירה נשמרה. ממתין לאישור מנהל.');
        }
    }

    /**
     * Display payment info page for pending payment.
     */
    public function paymentInfo()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();
        
        return view('trainer-payment-info', compact('trainer'));
    }

    /**
     * Display trial info and payment instructions for the authenticated trainer.
     */
    public function trialInfo()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();
        
        return view('trainer-trial-info', compact('trainer'));
    }

    /**
     * Display the specified trainer.
     */
    public function show(Trainer $trainer)
    {
        // Allow viewing active and trial trainers (trial is like a demo of paid subscription)
        if (!in_array($trainer->status, ['active', 'trial'])) {
            abort(404);
        }

        $trainer->load(['reviews', 'profileImage', 'likes']);
        $trainer->average_rating = $trainer->average_rating;
        $trainer->rating_count = $trainer->rating_count;
        
        // Check if current user liked this trainer
        $isLiked = Auth::check() ? $trainer->likedBy(Auth::user()) : false;

        // Track profile view
        try {
            TrainerProfileView::create([
                'trainer_id' => $trainer->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => Auth::id(),
                'viewed_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if tracking fails
            \Log::warning('Failed to track trainer profile view: ' . $e->getMessage());
        }

        return view('trainer-profile', compact('trainer', 'isLiked'));
    }

    /**
     * Display welcome page after trainer registration.
     */
    public function welcome()
    {
        $trainer = Trainer::where('owner_email', Auth::user()->email)->firstOrFail();
        
        return view('trainer-welcome', compact('trainer'));
    }

    /**
     * Remove the specified trainer from storage.
     */
    public function destroy(Trainer $trainer)
    {
        $trainer->delete();

        return redirect()->route('admin.trainers.index')
            ->with('success', 'המאמן נמחק בהצלחה.');
    }
}
