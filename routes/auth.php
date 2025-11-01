<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Route::get('register', fn() => Volt::render('pages.auth.register'))->name('register');
    Route::get('login', fn() => Volt::render('pages.auth.login'))->name('login');
    Route::get('forgot-password', fn() => Volt::render('pages.auth.forgot-password'))->name('password.request');
    Route::get('reset-password/{token}', fn($token) => Volt::render('pages.auth.reset-password', ['token' => $token]))->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', fn() => Volt::render('pages.auth.verify-email'))->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', fn() => Volt::render('pages.auth.confirm-password'))->name('password.confirm');
});
