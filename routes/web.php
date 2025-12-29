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

// Health check endpoint for Railway (DB-independent)
Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});

// Route to serve storage files - IMPROVED VERSION
// This route MUST be before other routes to catch storage requests
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
})->where('path', '.*'); // Accept any path

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

Route::get('/', [PageController::class, 'welcome'])->name('welcome');

// Public pages routes
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

// Google Auth routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// Language switching
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

// Public routes
// Auth routes are handled by Laravel Breeze in routes/auth.php
// The routes are loaded in bootstrap/app.php

// Public trainer routes - anyone can view trainers without authentication
Route::get('/trainers', [TrainerController::class, 'index'])->name('trainers.index');
Route::get('/trainers/{trainer}', [TrainerController::class, 'show'])->name('trainers.show');

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
