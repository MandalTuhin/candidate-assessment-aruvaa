<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )->withMiddleware(function(Middleware $middleware){
        // Essential: Trust all proxies for Railway.
        $middleware->trustProxies(at: '*');
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle file upload size exceeded errors
        $exceptions->render(function (\Symfony\Component\HttpFoundation\File\Exception\FileException $e) {
            return back()->with('error', 'File upload failed. Please ensure your file is under 2MB and try again.');
        });

        // Handle database connection errors
        $exceptions->render(function (\Illuminate\Database\QueryException $e) {
            \Log::error('Database connection error: '.$e->getMessage());

            return back()->with('error', 'Database connection failed. Please check your internet connection and try again.');
        });

        // Handle file size exceeded errors specifically
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            return back()->with('error', 'The uploaded file is too large. Maximum file size is 2MB.');
        });
    })
    ->create();
