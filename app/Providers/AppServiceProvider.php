<?php

namespace App\Providers;

use App\Repositories\EloquentQuestionRepository;
use App\Repositories\QuestionRepositoryInterface;
use App\Services\AssessmentService;
use App\Services\FileUploadService;
use App\Services\ScoringService;
use App\Services\SessionService;
use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider
 *
 * Main application service provider for the Laravel Assessment System.
 * Handles application-wide service registration and bootstrapping.
 * Registers all custom services and repositories for dependency injection.
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
     * and is used to bind services into the service container. Registers
     * all custom services and repositories for proper dependency injection.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->bind(QuestionRepositoryInterface::class, EloquentQuestionRepository::class);

        // Register services
        $this->app->singleton(SessionService::class);
        $this->app->singleton(ScoringService::class);
        $this->app->singleton(FileUploadService::class);
        
        // Register main assessment service with dependencies
        $this->app->singleton(AssessmentService::class, function ($app) {
            return new AssessmentService(
                $app->make(QuestionRepositoryInterface::class),
                $app->make(SessionService::class),
                $app->make(ScoringService::class)
            );
        });
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
