<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

class VoltServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Mount Volt in register() to ensure it's available before routes are loaded
        // Use absolute path to avoid issues with resource_path() timing
        Volt::mount([
            base_path('resources/views/livewire'),
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
