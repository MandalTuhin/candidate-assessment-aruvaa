<?php

use App\Http\Controllers\AssessmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AssessmentController::class, 'index'])->name('home');
