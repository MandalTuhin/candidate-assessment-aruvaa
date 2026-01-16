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
        // Retrieve the IDs we saved in the session
        $selectedIds = session('selected_languages', []);

        // If the session is empty (e.g. user goes directly to /assessment), redirect back
        if (empty($selectedIds)) {
            return redirect()->route('home');
        }

        // Fetch questions belonging to those languages
        // We use with('language') so we know which language each question belongs to
        $questions = \App\Models\Question::with('language')
            ->whereIn('language_id', $selectedIds)
            ->inRandomOrder()
            ->get();

        // 4. Pass the questions to a new view
        return view('assessment', compact('questions'));
    }
}
