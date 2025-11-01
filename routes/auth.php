<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    // Use standard Laravel view routes - Volt will auto-detect and render
    Route::get('register', function () {
        return view('livewire.pages.auth.register');
    })->name('register');

    Route::get('login', function () {
        return view('livewire.pages.auth.login');
    })->name('login');

    Route::get('forgot-password', function () {
        return view('livewire.pages.auth.forgot-password');
    })->name('password.request');

    Route::get('reset-password/{token}', function ($token) {
        return view('livewire.pages.auth.reset-password', ['token' => $token]);
    })->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', function () {
        return view('livewire.pages.auth.verify-email');
    })->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', function () {
        return view('livewire.pages.auth.confirm-password');
    })->name('password.confirm');
});
