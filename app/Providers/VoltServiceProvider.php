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
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Mount Volt views directory using absolute path
        // This ensures it works both in local development and production
        Volt::mount([
            base_path('resources/views/livewire'),
        ]);
    }
}
