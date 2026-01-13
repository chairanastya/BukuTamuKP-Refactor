<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Performance optimization: Disable Blade component generation
        // Icons can still be used via @svg('icon-name') directive
        // This prevents Laravel from scanning and registering thousands of icon components
    }
}
