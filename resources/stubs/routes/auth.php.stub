<?php

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Login
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    // Registration
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    // Forgot / Reset Password (throttled against spam / brute-force)
    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('password.store');
    // Two-Factor Authentication Challenge (throttled against brute-force)
    Route::get('two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorAuthenticatedSessionController::class, 'create'])
        ->name('two-factor.login');
    Route::post('two-factor-challenge', [\App\Http\Controllers\Auth\TwoFactorAuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Profile Management
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Two-Factor Authentication Management
    Route::post('user/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'store'])
        ->name('two-factor.enable');
    Route::post('user/confirmed-two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'confirm'])
        ->name('two-factor.confirm');
    Route::delete('user/two-factor-authentication', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'destroy'])
        ->name('two-factor.disable');
    Route::post('user/two-factor-recovery-codes', [\App\Http\Controllers\Auth\TwoFactorAuthenticationController::class, 'regenerateRecoveryCodes'])
        ->name('two-factor.regenerate-recovery-codes');

    // Email Verification
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Confirm Password (throttled against brute-force)
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('throttle:6,1');

    // Logout
    Route::post('logout', [LoginController::class, 'destroy'])
        ->name('logout');
});
