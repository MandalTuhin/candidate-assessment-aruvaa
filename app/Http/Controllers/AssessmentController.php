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

        // Get saved progress from session if exists
        $savedProgress = session('test_progress', []);

        // Prepare questions data for JavaScript
        $questionsData = $questions->map(function ($q) use ($savedProgress) {
            return [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'options' => $q->options,
                'correct_answer' => $q->correct_answer,
                'language_name' => $q->language->name,
                'selectedAnswer' => $savedProgress[$q->id] ?? null,
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
        $allQuestionsReview = [];

        // Compare answers and calculate analytics
        foreach ($questions as $index => $question) {
            $submittedAnswer = $userAnswers[$question->id] ?? null;
            $status = 'skipped';

            if ($submittedAnswer === null || $submittedAnswer === '') {
                $skippedCount++;
                $status = 'skipped';
            } elseif ($submittedAnswer === $question->correct_answer) {
                $correctCount++;
                $status = 'correct';
            } else {
                $incorrectCount++;
                $status = 'incorrect';
            }

            // Store all question details for review
            $allQuestionsReview[] = [
                'question_number' => $index + 1,
                'question_text' => $question->question_text,
                'language_name' => $question->language->name,
                'user_answer' => $submittedAnswer,
                'correct_answer' => $question->correct_answer,
                'options' => $question->options,
                'status' => $status,
            ];
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
            'all_questions_review' => $allQuestionsReview,
        ]);

        return redirect()->route('test.result');
    }

    public function showResult()
    {
        $score = session('last_score', 0);
        $assessmentId = session('assessment_id');
        $analytics = session('analytics', []);
        $allQuestionsReview = session('all_questions_review', []);

        // if there is no score in the session. we redirect to home
        if ($score === null) {
            return redirect()->route('home');
        }

        $threshold = 50;

        $passed = $score >= $threshold;

        return view('result', compact('score', 'passed', 'assessmentId', 'analytics', 'allQuestionsReview'));
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

    public function saveProgress(Request $request)
    {
        $answers = $request->input('answers', []);

        // Save progress to session
        session(['test_progress' => $answers]);

        return response()->json(['success' => true, 'message' => 'Progress saved']);
    }
}
