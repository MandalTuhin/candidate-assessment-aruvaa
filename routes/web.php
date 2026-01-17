<?php

use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Health check endpoint for Railway
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'database' => 'connected'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'database' => 'disconnected'
        ], 200);
    }
});

Route::get('/', [AssessmentController::class, 'index'])->name('home');
Route::post('/start-test', [AssessmentController::class, 'startTest'])->name('test.start');
Route::get('/assessment', [AssessmentController::class, 'showTest'])->name('test.show');
Route::post('/submit-test', [AssessmentController::class, 'submitTest'])->name('test.submit');
Route::get('/submit-expired', [AssessmentController::class, 'submitExpiredTest'])->name('test.submit-expired');
Route::get('/result', [AssessmentController::class, 'showResult'])->name('test.result');
Route::post('/upload-resume', [AssessmentController::class, 'uploadResume'])->name('resume.upload');
Route::post('/save-progress', [AssessmentController::class, 'saveProgress'])->name('progress.save');
