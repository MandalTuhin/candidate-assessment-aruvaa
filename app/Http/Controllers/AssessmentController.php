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
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'languages' => 'required|array|min:1',
        ]);

        // Session: Store the IDs so we remember them on the next page.
        session([
            'candidate_name' => $request->name,
            'candidate_email' => $request->email,
            'selected_languages' => $request->languages,
        ]);

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

        // Store question IDs in session for analytics calculation
        session(['test_question_ids' => $questions->pluck('id')->toArray()]);

        // Prepare questions data for JavaScript
        $questionsData = $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $q->options,
                'correct_answer' => $q->correct_answer,
                'language_name' => $q->language->name,
                'selectedAnswer' => null,
            ];
        })->values();

        // 4. Pass the questions to a new view
        return view('assessment', compact('questions', 'questionsData'));
    }

    public function submitTest(Request $request)
    {
        // Get user answers from the request
        $userAnswers = $request->input('answers', []); // format: [question_id => selected_option]

        // Get all question IDs from the test (including unanswered ones)
        $allQuestionIds = session('test_question_ids', []);
        $questions = \App\Models\Question::whereIn('id', $allQuestionIds)->get();

        $totalQuestions = $questions->count();
        $correctCount = 0;
        $incorrectCount = 0;
        $skippedCount = 0;
        $wrongAnswers = [];

        // Compare answers and calculate analytics
        foreach ($questions as $question) {
            $submittedAnswer = $userAnswers[$question->id] ?? null;

            if ($submittedAnswer === null || $submittedAnswer === '') {
                $skippedCount++;
            } elseif ($submittedAnswer === $question->correct_answer) {
                $correctCount++;
            } else {
                $incorrectCount++;
                // Store wrong answer details for review
                $wrongAnswers[] = [
                    'question_text' => $question->question_text,
                    'language_name' => $question->language->name,
                    'user_answer' => $submittedAnswer,
                    'correct_answer' => $question->correct_answer,
                    'options' => $question->options,
                ];
            }
        }

        // Calculate score (Percentage) - based on total questions
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        // Save the attempt to the database (Assessment Model)
        $assessment = \App\Models\Assessment::create([
            'candidate_name' => session('candidate_name'),
            'candidate_email' => session('candidate_email'),
            'score' => $score,
        ]);

        // Store analytics in session for result page
        session([
            'last_score' => $score,
            'assessment_id' => $assessment->id,
            'analytics' => [
                'total_questions' => $totalQuestions,
                'correct' => $correctCount,
                'incorrect' => $incorrectCount,
                'skipped' => $skippedCount,
                'answered' => $correctCount + $incorrectCount,
            ],
            'wrong_answers' => $wrongAnswers,
        ]);

        return redirect()->route('test.result');
    }

    public function showResult()
    {
        $score = session('last_score', 0);
        $assessmentId = session('assessment_id');
        $analytics = session('analytics', []);
        $wrongAnswers = session('wrong_answers', []);

        // if there is no score in the session. we redirect to home
        if ($score === null) {
            return redirect()->route('home');
        }

        $threshold = 50;

        $passed = $score >= $threshold;

        return view('result', compact('score', 'passed', 'assessmentId', 'analytics', 'wrongAnswers'));
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
                'resume_path' => $path,
            ]);

            return back()->with('success', 'Resume uploaded successfully!');
        }

        return back()->with('error', 'File upload failed.');
    }
}
