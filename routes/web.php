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
    $errorLog = [];
    
    try {
        $errorLog[] = '=== Test resume upload started ===';
        $errorLog[] = 'Files in request: ' . json_encode(array_keys($request->allFiles()));
        
        // Check if file exists
        if (!$request->hasFile('resume')) {
            $errorLog[] = 'ERROR: No resume file in request';
            return response()->json(['error' => 'No resume file uploaded', 'debug_log' => $errorLog], 400);
        }
        
        $file = $request->file('resume');
        $errorLog[] = 'File object created successfully';
        
        // Get file details safely
        $fileDetails = [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'is_valid' => $file->isValid(),
            'error_code' => $file->getError(),
        ];
        $errorLog[] = 'File details: ' . json_encode($fileDetails);
        
        // Test storage directory
        $storageDir = storage_path('app/public/resumes');
        $storageInfo = [
            'path' => $storageDir,
            'exists' => is_dir($storageDir),
            'writable' => is_writable($storageDir),
        ];
        $errorLog[] = 'Storage directory: ' . json_encode($storageInfo);
        
        // Try simple file storage first
        $errorLog[] = 'Attempting to store file...';
        $path = $file->store('test-resumes', 'public');
        $errorLog[] = 'File stored successfully at: ' . $path;
        
        // Check if file actually exists
        $fullPath = storage_path('app/public/' . $path);
        $fileExists = file_exists($fullPath);
        $errorLog[] = 'File existence check: path=' . $fullPath . ', exists=' . ($fileExists ? 'yes' : 'no');
        
        // Try creating assessment (this might be the issue)
        $errorLog[] = 'Creating test assessment...';
        $assessment = \App\Models\Assessment::create([
            'candidate_name' => 'Test User',
            'candidate_email' => 'test@example.com',
            'score' => 85,
        ]);
        $errorLog[] = 'Assessment created with ID: ' . $assessment->id;
        
        // Update assessment with resume path
        $errorLog[] = 'Updating assessment with resume path...';
        $assessment->update(['resume_path' => $path]);
        $errorLog[] = 'Assessment updated successfully';
        
        return response()->json([
            'success' => true,
            'assessment_id' => $assessment->id,
            'resume_path' => $path,
            'file_details' => $fileDetails,
            'file_exists_on_disk' => $fileExists,
            'full_path' => $fullPath,
            'debug_log' => $errorLog,
        ]);
        
    } catch (\Exception $e) {
        $errorLog[] = '=== EXCEPTION CAUGHT ===';
        $errorLog[] = 'Error message: ' . $e->getMessage();
        $errorLog[] = 'Error file: ' . $e->getFile();
        $errorLog[] = 'Error line: ' . $e->getLine();
        $errorLog[] = 'Error type: ' . get_class($e);
        
        // Try to log to Laravel log as well
        try {
            \Log::error('Test resume upload failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        } catch (\Exception $logError) {
            $errorLog[] = 'Failed to write to Laravel log: ' . $logError->getMessage();
        }
        
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => basename($e->getFile()),
            'type' => get_class($e),
            'debug_log' => $errorLog,
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
        <p><a href="/check-logs" style="color: #007cba;">Check Recent Logs</a></p>
    </body></html>')->header('Content-Type', 'text/html');
});

// Check recent logs
Route::get('/check-logs', function () {
    try {
        $logPaths = [
            storage_path('logs/laravel.log'),
            storage_path('logs/laravel-' . date('Y-m-d') . '.log'),
            '/var/log/laravel.log',
            '/tmp/laravel.log'
        ];
        
        $logInfo = [];
        foreach ($logPaths as $path) {
            $logInfo[] = [
                'path' => $path,
                'exists' => file_exists($path),
                'readable' => file_exists($path) && is_readable($path),
                'size' => file_exists($path) ? filesize($path) : 0
            ];
        }
        
        // Check storage/logs directory
        $logsDir = storage_path('logs');
        $logsDirInfo = [
            'logs_dir_exists' => is_dir($logsDir),
            'logs_dir_writable' => is_writable($logsDir),
            'logs_dir_contents' => is_dir($logsDir) ? scandir($logsDir) : []
        ];
        
        // Try to find any log file
        $foundLogFile = null;
        $logContent = '';
        
        foreach ($logPaths as $path) {
            if (file_exists($path) && is_readable($path)) {
                $foundLogFile = $path;
                $lines = file($path);
                $logContent = implode('', array_slice($lines, -30)); // Last 30 lines
                break;
            }
        }
        
        // If no file found, check if we can write to logs directory
        if (!$foundLogFile && is_dir($logsDir) && is_writable($logsDir)) {
            // Try to trigger a log entry
            \Log::info('Test log entry from check-logs route');
            
            // Check again
            foreach ($logPaths as $path) {
                if (file_exists($path)) {
                    $foundLogFile = $path;
                    $lines = file($path);
                    $logContent = implode('', array_slice($lines, -10));
                    break;
                }
            }
        }
        
        return response('<html><head><title>Log Check Results</title></head><body style="font-family: monospace; padding: 20px;">
            <h2>Log File Investigation</h2>
            
            <h3>Log Paths Checked:</h3>
            <pre>' . json_encode($logInfo, JSON_PRETTY_PRINT) . '</pre>
            
            <h3>Logs Directory Info:</h3>
            <pre>' . json_encode($logsDirInfo, JSON_PRETTY_PRINT) . '</pre>
            
            <h3>Log Channel Configuration:</h3>
            <pre>LOG_CHANNEL: ' . config('logging.default') . '
LOG_LEVEL: ' . config('logging.level', 'debug') . '
APP_ENV: ' . config('app.env') . '</pre>
            
            ' . ($foundLogFile ? '<h3>Recent Log Content (' . $foundLogFile . '):</h3>
            <pre style="background: #f5f5f5; padding: 15px; max-height: 400px; overflow-y: auto;">' . 
            htmlspecialchars($logContent) . '</pre>' : '<p><strong>No readable log file found</strong></p>') . '
            
            <p><a href="/upload-test-form">← Back to Upload Test</a></p>
        </body></html>')->header('Content-Type', 'text/html');
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
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
