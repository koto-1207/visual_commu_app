<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/dashboard', '/places')->name('dashboard');
    Route::view('/profile', 'profile')->name('profile');
    Volt::route('/places', 'places.index')->name('places.index');
    Volt::route('/places/create', 'places.create')->name('places.create');
})->name('admin.');

Volt::route('/schedule', 'schedule.index')->name('schedule');
Volt::route('/choice', 'choice.index')->name('choice');
Volt::route('/', 'welcome')->name('welcome');
