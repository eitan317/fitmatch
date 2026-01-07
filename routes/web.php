<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainerDashboardController;
use App\Http\Controllers\TrainerLikeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\TrainerController as AdminTrainerController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SitemapController;

// Health check endpoint for Railway (DB-independent)
Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});

// Sitemap routes - MUST be at the very top, before ANY other routes
// Exclude session middleware since sitemaps don't need sessions (and may not have DB connection)
// This prevents database connection errors when DB is unavailable
Route::withoutMiddleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\Session\Middleware\AuthenticateSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class, // Requires session
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, // Requires session for CSRF
    \App\Http\Middleware\SetLocale::class, // Also exclude SetLocale since it uses Session
    \App\Http\Middleware\TrackPageViews::class, // Exclude tracking since it may use DB
])->group(function () {
    Route::get('/sitemap.xml', function() {
        \Log::info('Sitemap.xml route hit', [
            'uri' => request()->getRequestUri(),
            'static_file_exists' => file_exists(public_path('sitemap.xml')),
        ]);
        
        // If static file exists, log warning but still use route
        if (file_exists(public_path('sitemap.xml'))) {
            \Log::warning('Static sitemap.xml file exists but route is being used');
        }
        
        return app(\App\Http\Controllers\SitemapController::class)->main();
    })->name('sitemap.main');

    Route::get('/sitemap-trainers.xml', [SitemapController::class, 'trainers'])->name('sitemap.trainers');
    Route::get('/sitemap-index.xml', [SitemapController::class, 'index'])->name('sitemap.index');

    // Alternative route without .xml extension (fallback if needed)
    Route::get('/sitemap', [SitemapController::class, 'main'])->name('sitemap.alt');
});

// Route to serve storage files - IMPROVED VERSION
// This route MUST be after sitemap routes to avoid catching sitemap.xml
Route::get('/storage/{path}', function ($path) {
    // Security: Prevent directory traversal
    $path = str_replace('..', '', $path);
    $path = str_replace('\\', '/', $path);
    $path = ltrim($path, '/');
    
    // Normalize path - remove duplicate slashes
    $path = preg_replace('#/+#', '/', $path);
    
    // Try multiple locations for the file
    $possiblePaths = [
        storage_path('app/public/' . $path), // Primary location - where files are stored
        public_path('storage/' . $path), // Symlink location - if symlink exists
        storage_path('app/' . $path), // Alternative location
    ];
    
    $filePath = null;
    
    // Try to find the file in any of the possible locations
    foreach ($possiblePaths as $possiblePath) {
        // Normalize the path
        $possiblePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $possiblePath);
        if (file_exists($possiblePath) && is_file($possiblePath)) {
            $fileSize = @filesize($possiblePath);
            // Accept file even if size is 0 (might be valid)
            if ($fileSize !== false) {
                $filePath = $possiblePath;
                break;
            }
        }
    }
    
    // If file not found, return 404
    if (!$filePath) {
        if (config('app.debug')) {
            \Log::warning('Storage route: File not found', [
                'requested_path' => $path,
                'possible_paths' => $possiblePaths,
            ]);
        }
        abort(404, 'File not found: ' . basename($path));
    }
    
    // Security: Ensure file is within allowed directories
    $publicStoragePath = realpath(storage_path('app/public')) ?: storage_path('app/public');
    $realFilePath = realpath($filePath);
    $realPublicPath = realpath($publicStoragePath);
    
    if (!$realFilePath) {
        abort(404, 'File path could not be resolved');
    }
    
    // Check if file is within allowed directories
    $isInStorage = $realPublicPath && str_starts_with($realFilePath, $realPublicPath);
    
    if (!$isInStorage) {
        // Also check public/storage symlink
        $publicStorageSymlink = realpath(public_path('storage'));
        if (!$publicStorageSymlink || !str_starts_with($realFilePath, $publicStorageSymlink)) {
            if (config('app.debug')) {
                \Log::warning('Storage route: Path outside allowed directories', [
                    'file_path' => $filePath,
                    'real_file_path' => $realFilePath,
                ]);
            }
            abort(403, 'Access denied');
        }
    }
    
    // Detect MIME type - accept any file type!
    $mimeType = @mime_content_type($filePath);
    if (!$mimeType) {
        // Fallback based on extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    }
    
    // Return file with proper headers
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '^(?!sitemap).*'); // Accept any path except sitemap files

// Temporary route to download hero image (remove after use)
Route::get('/download-hero-image', function () {
    $url = "https://media.istockphoto.com/id/972833328/photo/male-personal-trainer-helping-sportswoman-to-do-exercises-with-barbell-at-gym.jpg?s=612x612&w=0&k=20&c=5kIxaobVDjjDrYvv8qNB2lGJoBImzHvj-csu30o_lZY=";
    $dir = public_path('images');
    $output = $dir . '/hero-trainers.jpg';
    
    // Create directory if it doesn't exist
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Download image
    $imageData = @file_get_contents($url);
    if ($imageData !== false) {
        file_put_contents($output, $imageData);
        return response()->json(['success' => true, 'message' => 'Image downloaded successfully', 'path' => $output]);
    } else {
        return response()->json(['success' => false, 'message' => 'Failed to download image'], 500);
    }
})->name('download.hero.image');

// Dynamic robots.txt
Route::get('/robots.txt', function () {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Disallow: /admin/\n";
    $content .= "Disallow: /trainer/dashboard\n\n";
    // sitemap - use .php file since php artisan serve doesn't route .xml to Laravel
    $content .= "Sitemap: " . config('app.url') . "/sitemap.php\n";
    $content .= "Sitemap: " . config('app.url') . "/sitemap.xml\n"; // Also try route
    $content .= "Sitemap: " . config('app.url') . "/sitemap\n"; // Fallback
    
    return response($content, 200)
        ->header('Content-Type', 'text/plain');
})->name('robots.txt');

// Language switching (must be before language-prefixed routes)
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

// Google Auth routes (no language prefix needed)
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// Public routes with optional language prefix
// Define routes both with and without language prefix for backward compatibility
$supportedLocales = ['he', 'en', 'ru', 'ar'];

// Routes without language prefix (backward compatibility - default to Hebrew)
Route::get('/', [PageController::class, 'welcome'])->name('welcome');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/trainers', [TrainerController::class, 'index'])->name('trainers.index');
Route::get('/trainers/{trainer}', [TrainerController::class, 'show'])->name('trainers.show');

// Routes with language prefix (SEO-friendly URLs)
foreach ($supportedLocales as $locale) {
    Route::prefix($locale)->group(function () use ($locale) {
        Route::get('/', [PageController::class, 'welcome'])->name("welcome.{$locale}");
        Route::get('/about', [PageController::class, 'about'])->name("about.{$locale}");
        Route::get('/faq', [PageController::class, 'faq'])->name("faq.{$locale}");
        Route::get('/contact', [PageController::class, 'contact'])->name("contact.{$locale}");
        Route::post('/contact', [PageController::class, 'storeContact'])->name("contact.store.{$locale}");
        Route::get('/privacy', [PageController::class, 'privacy'])->name("privacy.{$locale}");
        Route::get('/terms', [PageController::class, 'terms'])->name("terms.{$locale}");
        Route::get('/trainers', [TrainerController::class, 'index'])->name("trainers.index.{$locale}");
        Route::get('/trainers/{trainer}', [TrainerController::class, 'show'])->name("trainers.show.{$locale}");
    });
}

// Protected routes - require authentication
Route::middleware('auth')->group(function () {
    // Trainer like route
    Route::post('/trainers/{trainer}/like', [TrainerLikeController::class, 'toggle'])->name('trainers.like');
    // Trainer registration and management routes
    Route::get('/register-trainer', [TrainerController::class, 'create'])->name('trainers.create');
    Route::post('/register-trainer', [TrainerController::class, 'store'])->name('trainers.store');
    Route::get('/trainer/welcome', [TrainerController::class, 'welcome'])->name('trainers.welcome');

    // Trainer dashboard routes
    Route::prefix('trainer')->name('trainer.')->group(function () {
        Route::get('/dashboard', [TrainerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/reviews', [TrainerDashboardController::class, 'reviews'])->name('reviews');
        Route::get('/statistics', [TrainerDashboardController::class, 'statistics'])->name('statistics');
        Route::get('/profile/edit', [TrainerDashboardController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile/update', [TrainerDashboardController::class, 'updateProfile'])->name('profile.update');
    });
    
    // Old routes - commented out (no longer used)
    // Route::get('/trainer/choose-plan', [TrainerController::class, 'choosePlan'])->name('trainers.choose-plan');
    // Route::post('/trainer/choose-plan', [TrainerController::class, 'storePlanChoice'])->name('trainers.store-plan-choice');
    // Route::get('/trainer/trial-info', [TrainerController::class, 'trialInfo'])->name('trainers.trial-info');
    // Route::get('/trainer/payment-info', [TrainerController::class, 'paymentInfo'])->name('trainers.payment-info');

    // Review routes
    Route::post('/trainers/{trainer}/reviews', [ReviewController::class, 'store'])->name('reviews.store');


    // Admin review management routes
    Route::middleware('auth')->group(function () {
        Route::patch('/reviews/{review}/rating', [ReviewController::class, 'updateRating'])->name('reviews.update-rating');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Subscription routes
    Route::get('/subscriptions/choose', [\App\Http\Controllers\SubscriptionController::class, 'choosePlan'])->name('subscriptions.choose');
    Route::post('/subscriptions/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::get('/subscriptions/my-subscription', [\App\Http\Controllers\SubscriptionController::class, 'mySubscription'])->name('subscriptions.my-subscription');
    Route::post('/subscriptions/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('/subscriptions/renew', [\App\Http\Controllers\SubscriptionController::class, 'renew'])->name('subscriptions.renew');

    // Payment routes
    Route::get('/subscriptions/payment/{subscription}', [\App\Http\Controllers\PaymentController::class, 'payment'])->name('subscriptions.payment');
    Route::post('/subscriptions/payment/process/{subscription}', [\App\Http\Controllers\PaymentController::class, 'processPayment'])->name('subscriptions.payment.process');
    Route::get('/subscriptions/payment/success/{subscription}', [\App\Http\Controllers\PaymentController::class, 'paymentSuccess'])->name('subscriptions.payment-success');
    Route::get('/subscriptions/payment/failed/{subscription}', [\App\Http\Controllers\PaymentController::class, 'paymentFailed'])->name('subscriptions.payment-failed');
    Route::post('/subscriptions/webhook', [\App\Http\Controllers\PaymentController::class, 'webhook'])->name('subscriptions.webhook');
});

// Admin routes - require admin authentication
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/trainers', [AdminTrainerController::class, 'index'])->name('trainers.index');
    Route::post('/trainers/cleanup-all', [AdminTrainerController::class, 'cleanupAll'])->name('trainers.cleanup-all');
    Route::get('/trainers/{trainer}/edit', [AdminTrainerController::class, 'edit'])->name('trainers.edit');
    Route::post('/trainers/{trainer}/update', [AdminTrainerController::class, 'update'])->name('trainers.update');
    Route::get('/trainers/{trainer}/upload-image', [AdminTrainerController::class, 'showUploadImage'])->name('trainers.upload-image');
    Route::post('/trainers/{trainer}/upload-image', [AdminTrainerController::class, 'uploadImage'])->name('trainers.upload-image.store');
    Route::post('/trainers/{trainer}/approve', [AdminTrainerController::class, 'approve'])->name('trainers.approve');
    Route::post('/trainers/{trainer}/approve-payment', [AdminTrainerController::class, 'approvePayment'])->name('trainers.approve-payment');
    Route::post('/trainers/{trainer}/reject', [AdminTrainerController::class, 'reject'])->name('trainers.reject');
    Route::post('/trainers/{trainer}/block', [AdminTrainerController::class, 'block'])->name('trainers.block');
    Route::post('/trainers/{trainer}/unblock', [AdminTrainerController::class, 'unblock'])->name('trainers.unblock');
    Route::post('/trainers/{trainer}/change-subscription', [AdminTrainerController::class, 'changeSubscription'])->name('trainers.change-subscription');
    Route::delete('/trainers/{trainer}', [AdminTrainerController::class, 'destroy'])->name('trainers.destroy');
});

// Legacy routes for backward compatibility
Route::middleware(['auth', 'admin'])->get('/admin', [AdminTrainerController::class, 'index']);
Route::get('/trainer-profile', function () {
    return redirect()->route('trainers.index');
});
