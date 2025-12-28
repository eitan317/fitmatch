<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Review;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

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
            ->with(['reviews', 'subscriptionPlan'])
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
            // הסרת כל ההגבלות - כל קובץ יתקבל!
            'profile_image' => 'nullable|file', // רק בודק שזה קובץ, לא משנה מה
        ], [
            'age.min' => 'הגיל המינימלי המותר הוא 18',
            'age.integer' => 'הגיל חייב להיות מספר שלם',
            // הסרת כל הודעות השגיאה על תמונה
        ]);

        // Note: Training types validation will be done after subscription is selected
        // This validation is handled in the subscription flow

        // Get owner email from authenticated user
        $ownerEmail = Auth::user()->email;

        // Handle profile image upload - NO RESTRICTIONS!
        $profileImagePath = null;
        
        if ($request->hasFile('profile_image')) {
            try {
                $file = $request->file('profile_image');
                
                // לא בודקים אם הקובץ תקין - כל קובץ יתקבל!
                // רק בודקים שיש קובץ
                if ($file && $file->getSize() > 0) {
                    
                    // קבל את הסיומת המקורית או השתמש ב-extension
                    $originalExtension = $file->getClientOriginalExtension();
                    if (empty($originalExtension)) {
                        // אם אין סיומת, נסה לזהות לפי MIME type
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
                    
                    // Generate unique filename - תמיד עם סיומת
                    $filename = time() . '_' . uniqid() . '.' . $originalExtension;
                    
                    // Ensure trainers directory exists with proper permissions
                    $trainersPath = storage_path('app/public/trainers');
                    if (!File::exists($trainersPath)) {
                        File::makeDirectory($trainersPath, 0755, true);
                    }
                    
                    // בדוק הרשאות - אם אין הרשאות, נסה לתקן
                    if (!is_writable($trainersPath)) {
                        @chmod($trainersPath, 0755);
                    }
                    
                    // Method 1: Try move_uploaded_file - הכי אמין!
                    $fullPath = $trainersPath . DIRECTORY_SEPARATOR . $filename;
                    $saved = false;
                    
                    // נסה עם move_uploaded_file (עובד ישירות עם הקובץ שהועלה)
                    if (function_exists('move_uploaded_file')) {
                        $tmpPath = $file->getRealPath();
                        if ($tmpPath && file_exists($tmpPath)) {
                            if (@move_uploaded_file($tmpPath, $fullPath)) {
                                $saved = true;
                                $profileImagePath = 'trainers/' . $filename;
                            }
                        }
                    }
                    
                    // Method 2: Fallback - use Laravel's move method
                    if (!$saved) {
                        try {
                            $movedPath = $file->move($trainersPath, $filename);
                            if ($movedPath && file_exists($fullPath)) {
                                $saved = true;
                                $profileImagePath = 'trainers/' . $filename;
                            }
                        } catch (\Exception $e) {
                            \Log::warning('File move failed, trying storeAs', [
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                    
                    // Method 3: Fallback - use storeAs
                    if (!$saved) {
                        try {
                            $profileImagePath = $file->storeAs('trainers', $filename, 'public');
                            if ($profileImagePath) {
                                $fullPath = storage_path('app/public/' . $profileImagePath);
                                if (file_exists($fullPath)) {
                                    $saved = true;
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::warning('storeAs failed', [
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                    
                    // Method 4: Last resort - copy file content directly
                    if (!$saved) {
                        try {
                            $fileContent = file_get_contents($file->getRealPath());
                            if ($fileContent !== false && file_put_contents($fullPath, $fileContent) !== false) {
                                $saved = true;
                                $profileImagePath = 'trainers/' . $filename;
                            }
                        } catch (\Exception $e) {
                            \Log::error('All file save methods failed', [
                                'error' => $e->getMessage(),
                                'filename' => $filename,
                                'trainers_path' => $trainersPath,
                            ]);
                        }
                    }
                    
                    // Verify file was saved
                    if ($saved && file_exists($fullPath) && filesize($fullPath) > 0) {
                        // Set proper permissions
                        @chmod($fullPath, 0644);
                        
                        \Log::info('Image saved successfully - NO RESTRICTIONS', [
                            'path' => $profileImagePath,
                            'full_path' => $fullPath,
                            'file_size' => filesize($fullPath),
                            'file_type' => $file->getMimeType(),
                            'original_name' => $file->getClientOriginalName(),
                        ]);
                    } else {
                        // אם לא נשמר - לא נכשל, פשוט לא נשמור תמונה
                        \Log::warning('Image could not be saved, continuing without image', [
                            'filename' => $filename,
                            'trainers_path' => $trainersPath,
                        ]);
                        $profileImagePath = null;
                    }
                }
            } catch (\Exception $e) {
                // לא נכשל - פשוט נמשיך בלי תמונה
                \Log::error('Profile image upload error (non-critical): ' . $e->getMessage());
                $profileImagePath = null;
            }
        }

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
            'profile_image_path' => $profileImagePath, // הוספת נתיב התמונה
            'status' => 'pending',
            'approved_by_admin' => false,
        ];

        // Don't include 'name' field - we use 'full_name' instead
        // If 'name' column exists and is required, it should be made nullable via migration
        // or removed from the table entirely

        $trainer = Trainer::create($trainerData);

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

        $trainer->load('reviews');
        $trainer->average_rating = $trainer->average_rating;
        $trainer->rating_count = $trainer->rating_count;

        return view('trainer-profile', compact('trainer'));
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
