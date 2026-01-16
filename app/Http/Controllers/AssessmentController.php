<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        // Get data from Model
        $languages = Language::all();

        // pass data to View
        return view('welcome', compact('languages'));
    }
}
