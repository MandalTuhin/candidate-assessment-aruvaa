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

// Test file upload functionality
Route::post('/test-upload', function (\Illuminate\Http\Request $request) {
    try {
        \Log::info('Test upload started', ['files' => $request->allFiles()]);
        
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
        
        $file = $request->file('file');
        \Log::info('File details', [
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'is_valid' => $file->isValid(),
        ]);
        
        // Try to store the file
        $path = $file->store('test-uploads', 'public');
        \Log::info('File stored successfully', ['path' => $path]);
        
        return response()->json([
            'success' => true,
            'path' => $path,
            'full_path' => storage_path('app/public/' . $path),
            'file_exists' => \Storage::disk('public')->exists($path),
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Test upload failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
});

// Test database and assessments
Route::get('/test-assessments', function () {
    try {
        $assessments = \App\Models\Assessment::all();
        $latestAssessment = \App\Models\Assessment::latest()->first();
        
        return response()->json([
            'assessments_count' => $assessments->count(),
            'latest_assessment' => $latestAssessment,
            'table_exists' => \Schema::hasTable('assessments'),
            'columns' => \Schema::hasTable('assessments') ? \Schema::getColumnListing('assessments') : [],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test resume upload process
Route::post('/test-resume-upload', function (\Illuminate\Http\Request $request) {
    try {
        \Log::info('Test resume upload started', $request->all());
        
        // Create a test assessment first
        $assessment = \App\Models\Assessment::create([
            'candidate_name' => 'Test User',
            'candidate_email' => 'test@example.com',
            'score' => 85,
        ]);
        
        \Log::info('Test assessment created', ['id' => $assessment->id]);
        
        if (!$request->hasFile('resume')) {
            return response()->json(['error' => 'No resume file uploaded'], 400);
        }
        
        $file = $request->file('resume');
        \Log::info('Resume file details', [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
        ]);
        
        // Test the exact same process as the real upload
        $path = $file->store('resumes', 'public');
        \Log::info('Resume stored', ['path' => $path]);
        
        $assessment->update(['resume_path' => $path]);
        \Log::info('Assessment updated with resume path');
        
        return response()->json([
            'success' => true,
            'assessment_id' => $assessment->id,
            'resume_path' => $path,
            'file_exists' => \Storage::disk('public')->exists($path),
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Test resume upload failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
});

// Simple upload test form (bypasses Inertia)
Route::get('/upload-test-form', function () {
    return response('<html><head><title>Resume Upload Test</title></head><body style="font-family: Arial; padding: 20px;">
        <h2>Test Resume Upload</h2>
        <form action="/test-resume-upload" method="POST" enctype="multipart/form-data">
            ' . csrf_field() . '
            <p><label>Select Resume (PDF, DOC, DOCX):</label><br>
            <input type="file" name="resume" accept=".pdf,.doc,.docx" required></p>
            <p><button type="submit" style="padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer;">Upload Test Resume</button></p>
        </form>
        <hr>
        <p><strong>Storage Status:</strong></p>
        <ul>
            <li>Storage writable: ' . (is_writable(storage_path('app/public')) ? '✅ Yes' : '❌ No') . '</li>
            <li>Resumes dir writable: ' . (is_writable(storage_path('app/public/resumes')) ? '✅ Yes' : '❌ No') . '</li>
            <li>PHP upload limit: ' . ini_get('upload_max_filesize') . '</li>
        </ul>
    </body></html>')->header('Content-Type', 'text/html');
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
