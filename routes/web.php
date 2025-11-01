<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Load auth routes first to ensure they're registered at the same time
require __DIR__ . '/auth.php';

Volt::route('/', 'welcome')->name('welcome');
Volt::route('/schedule', 'schedule.index')->name('schedule');
Volt::route('/choice', 'choice.index')->name('choice');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/dashboard', '/places')->name('dashboard');
    Route::view('/profile', 'profile')->name('profile');
    Volt::route('/places', 'places.index')->name('places.index');
    Volt::route('/places/create', 'places.create')->name('places.create');
});
