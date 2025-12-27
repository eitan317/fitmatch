<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\TrainerController as AdminTrainerController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LanguageController;

// Health check endpoint for Railway (DB-independent)
Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});

// Fallback route to serve storage files if symbolic link doesn't work
Route::get('/storage/{path}', function ($path) {
    // Security: Prevent directory traversal
    $path = str_replace('..', '', $path);
    $path = ltrim($path, '/');
    
    $filePath = storage_path('app/public/' . $path);
    
    // Log for debugging
    \Log::info('Storage route accessed', [
        'requested_path' => $path,
        'file_path' => $filePath,
        'file_exists' => file_exists($filePath),
        'is_file' => is_file($filePath)
    ]);
    
    // Security: Ensure file is within public storage directory
    $publicStoragePath = storage_path('app/public');
    $realFilePath = realpath($filePath);
    $realPublicPath = realpath($publicStoragePath);
    
    if (!$realFilePath || !$realPublicPath) {
        \Log::warning('Storage route: Invalid paths', [
            'file_path' => $filePath,
            'real_file_path' => $realFilePath,
            'real_public_path' => $realPublicPath
        ]);
        abort(404);
    }
    
    if (!str_starts_with($realFilePath, $realPublicPath)) {
        \Log::warning('Storage route: Path outside public storage', [
            'file_path' => $filePath,
            'real_file_path' => $realFilePath,
            'real_public_path' => $realPublicPath
        ]);
        abort(404);
    }
    
    if (!file_exists($filePath) || !is_file($filePath)) {
        \Log::warning('Storage route: File not found', [
            'requested_path' => $path,
            'file_path' => $filePath,
            'file_exists' => file_exists($filePath),
            'is_file' => is_file($filePath),
            'directory_listing' => is_dir(dirname($filePath)) ? array_slice(scandir(dirname($filePath)), 2) : 'directory_not_exists'
        ]);
        abort(404);
    }
    
    $mimeType = mime_content_type($filePath);
    if (!$mimeType) {
        $mimeType = 'application/octet-stream';
    }
    
    \Log::info('Storage route: Serving file', [
        'file_path' => $filePath,
        'mime_type' => $mimeType,
        'file_size' => filesize($filePath)
    ]);
    
    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

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
    // Trainer registration and management routes
    Route::get('/register-trainer', [TrainerController::class, 'create'])->name('trainers.create');
    Route::post('/register-trainer', [TrainerController::class, 'store'])->name('trainers.store');
    Route::get('/trainer/choose-plan', [TrainerController::class, 'choosePlan'])->name('trainers.choose-plan');
    Route::post('/trainer/choose-plan', [TrainerController::class, 'storePlanChoice'])->name('trainers.store-plan-choice');
    Route::get('/trainer/trial-info', [TrainerController::class, 'trialInfo'])->name('trainers.trial-info');
    Route::get('/trainer/payment-info', [TrainerController::class, 'paymentInfo'])->name('trainers.payment-info');

    // Review routes
    Route::post('/trainers/{trainer}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

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
    Route::delete('/trainers/{trainer}', [AdminTrainerController::class, 'destroy'])->name('trainers.destroy');
});

// Legacy routes for backward compatibility
Route::middleware(['auth', 'admin'])->get('/admin', [AdminTrainerController::class, 'index']);
Route::get('/trainer-profile', function () {
    return redirect()->route('trainers.index');
});
