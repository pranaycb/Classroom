<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;

Route::middleware('guest')->group(function () {

    /**
     * Routes for registration
     */
    Route::get('register', [RegistrationController::class, 'index'])->name('register');
    Route::post('register', [RegistrationController::class, 'store']);

    /**
     * Routes for login
     */
    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    /**
     * Routes for forgot & reset password
     */
    Route::get('forgot-password', [ForgotPasswordController::class, 'index'])
        ->name('password.request');

    Route::post('forgot-password', [ForgotPasswordController::class, 'handleResetRequest'])
        ->name('password.email');

    Route::get('reset-password/{token}', [ForgotPasswordController::class, 'reset'])
        ->name('password.reset');

    Route::post('reset-password', [ForgotPasswordController::class, 'handleReset'])
        ->name('password.store');

    /**
     * Route for logout
     */
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout')
        ->middleware('auth')->withoutMiddleware('guest');
});

Route::middleware('auth')->group(function () {

    Route::get('verify-email', [EmailVerificationController::class, 'index'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmPasswordController::class, 'index'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmPasswordController::class, 'store']);
});
