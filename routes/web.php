<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\TrainerController as AdminTrainerController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\PageController;

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

// Public routes
// Auth routes are handled by Laravel Breeze in routes/auth.php
// The routes are loaded in bootstrap/app.php

// Protected routes - require authentication
Route::middleware('auth')->group(function () {
    // Trainer routes
    Route::get('/trainers', [TrainerController::class, 'index'])->name('trainers.index');
    Route::get('/trainers/{trainer}', [TrainerController::class, 'show'])->name('trainers.show');
    Route::get('/register-trainer', [TrainerController::class, 'create'])->name('trainers.create');
    Route::post('/register-trainer', [TrainerController::class, 'store'])->name('trainers.store');
    Route::get('/edit-trainer/{trainer}', [TrainerController::class, 'edit'])->name('trainers.edit');
    Route::post('/edit-trainer/{trainer}', [TrainerController::class, 'update'])->name('trainers.update');

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
    Route::post('/trainers/{trainer}/approve', [AdminTrainerController::class, 'approve'])->name('trainers.approve');
    Route::post('/trainers/{trainer}/reject', [AdminTrainerController::class, 'reject'])->name('trainers.reject');
    Route::delete('/trainers/{trainer}', [AdminTrainerController::class, 'destroy'])->name('trainers.destroy');
});

// Legacy routes for backward compatibility
Route::middleware(['auth', 'admin'])->get('/admin', [AdminTrainerController::class, 'index']);
Route::get('/trainer-profile', function () {
    return redirect()->route('trainers.index');
});
