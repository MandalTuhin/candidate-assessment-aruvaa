<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    /**
     * Display the language selection page.
     */
    public function index()
    {
        $languages = Language::all();
        return view('welcome', compact('languages'));
    }

    /**
     * Process the language selection and start the test.
     */
    public function startTest(Request $request)
    {
        // Validate: Ensure 'languages' is present, is an array, and has at least 1 item.
        $request->validate([
            'languages' => 'required|array|min:1',
        ]);

        // Session: Store the IDs so we remember them on the next page.
        session(['selected_languages' => $request->languages]);

        // Redirect: Go to the quiz display (we will create this route next).
        return redirect()->route('test.show');
    }

    public function showTest()
    {
        return "The quiz will appear here. Selected IDs: " . implode(', ', session('selected_languages', []));
    }
}
