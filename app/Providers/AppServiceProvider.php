<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider
 *
 * Main application service provider for the Laravel Assessment System.
 * Handles application-wide service registration and bootstrapping.
 * Currently uses default Laravel configuration but can be extended
 * for custom service bindings and application setup.
 *
 * @author Laravel Assessment System
 *
 * @version 1.0.0
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * This method is called during the application bootstrapping process
     * and is used to bind services into the service container. Currently
     * empty but available for future service registrations.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * This method is called after all service providers have been registered.
     * Used for performing actions that depend on other services being available.
     * Currently empty but available for future application bootstrapping.
     */
    public function boot(): void
    {
        //
    }
}
