<?php

use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Health check endpoint for Railway
Route::get('/health', function () {
    try {
        // Test database connection
        DB::connection()->getPdo();
        
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'database' => 'connected'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ok', // Still return ok even if DB fails
            'timestamp' => now(),
            'database' => 'disconnected',
            'error' => $e->getMessage()
        ], 200); // Return 200 so Railway doesn't fail health check
    }
});

// Simple debug endpoint
Route::get('/debug', function () {
    return response()->json([
        'message' => 'Laravel is running!',
        'timestamp' => now(),
        'env' => app()->environment(),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'app_url' => config('app.url'),
        'vite_manifest_exists' => file_exists(public_path('build/manifest.json')),
        'languages_count' => \App\Models\Language::count()
    ]);
});

// Test plain HTML route (no Inertia)
Route::get('/test-html', function () {
    return '<html><body><h1>Plain HTML Test - Laravel is working!</h1><p>If you see this, the server is fine.</p></body></html>';
});

// Test storage permissions
Route::get('/test-storage', function () {
    $storagePath = storage_path('app/public');
    $resumesPath = storage_path('app/public/resumes');
    $publicLink = public_path('storage');
    
    return response()->json([
        'storage_path_exists' => is_dir($storagePath),
        'storage_path_writable' => is_writable($storagePath),
        'resumes_path_exists' => is_dir($resumesPath),
        'resumes_path_writable' => is_writable($resumesPath),
        'public_link_exists' => is_link($publicLink) || is_dir($publicLink),
        'storage_path' => $storagePath,
        'resumes_path' => $resumesPath,
        'public_link' => $publicLink,
        'php_upload_max_filesize' => ini_get('upload_max_filesize'),
        'php_post_max_size' => ini_get('post_max_size'),
        'php_max_file_uploads' => ini_get('max_file_uploads'),
    ]);
});

Route::get('/', [AssessmentController::class, 'index'])->name('home');

// This handles the form submission from the landing page
Route::post('/start-test', [AssessmentController::class, 'startTest'])->name('test.start');

// The page where the questions are displayed
Route::get('/assessment', [AssessmentController::class, 'showTest'])->name('test.show');

// Routing to submit the test and get the results
Route::post('/submit-test', [AssessmentController::class, 'submitTest'])->name('test.submit');

// Route for handling expired tests
Route::get('/submit-expired', [AssessmentController::class, 'submitExpiredTest'])->name('test.submit-expired');

// Route for showing the result
Route::get('/result', [AssessmentController::class, 'showResult'])->name('test.result');

// Route for resume upload
Route::post('/upload-resume', [AssessmentController::class, 'uploadResume'])->name('resume.upload');

// Route for saving partial progress
Route::post('/save-progress', [AssessmentController::class, 'saveProgress'])->name('progress.save');
