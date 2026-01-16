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

    public function submitTest(Request $request)
    {
        // Get user answers from the request
        $userAnswers = $request->input('answers', []); // format: [question_id => selected_option]

        // Fetch the actual questions to compare answers
        $questionIds = array_keys($userAnswers);
        $questions = \App\Models\Question::whereIn('id', $questionIds)->get();

        $totalQuestions = $questions->count();
        $correctCount = 0;

        // Compare answers
        foreach ($questions as $question) {
            $submittedAnswer = $userAnswers[$question->id] ?? null;
            if ($submittedAnswer === $question->correct_answer) {
                $correctCount++;
            }
        }

        // Calculate score (Percentage)
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        // Save the attempt to the database (Assessment Model)
        // For now, Use 'Guest' as the name/email form is not built yet
        $assessment = \App\Models\Assessment::create([
            'candidate_name' => 'Guest Candidate',
            'candidate_email' => 'guest@example.com',
            'score' => $score,
        ]);

        // 6. Store score in session to show on result page
        session(['last_score' => $score, 'assessment_id' => $assessment->id]);

        return redirect()->route('test.result');
    }

    public function showResult()
    {
        $score = session('last_score', 0);
        $assessmentId = session('assessment_id');

        // if there is no score in the session. we redirect to home
        if ($score === null) {
            return redirect()->route('home');
        }

        $threshold = 50;

        $passed = $score >= $threshold;

        return view('result', compact('score', 'passed', 'assessmentId'));
    }

    public function uploadResume(Request $request)
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'assessment_id' => 'required|exists:assessments,id',
        ]);

        if ($request->hasFile('resume')) {
            // Store the file
            $path = $request->file('resume')->store('resumes', 'public');

            // Retrieve the specific model instance
            $assessment = \App\Models\Assessment::findOrFail($request->assessment_id);

            // Update the record
            $assessment->update([
                'resume_path' => $path
            ]);

            return back()->with('success', 'Resume uploaded successfully!');
        }

        return back()->with('error', 'File upload failed.');
    }
}
