<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

/**
 * AssessmentController
 *
 * Handles the complete candidate technical assessment workflow including
 * language selection, test execution, progress tracking, and result processing.
 *
 * @author Laravel Assessment System
 *
 * @version 1.0.0
 */
class AssessmentController extends Controller
{
    /**
     * Display the language selection page.
     *
     * Shows the initial landing page where candidates can select their preferred
     * programming languages for the technical assessment. Retrieves all available
     * languages from the database and passes them to the Vue.js Welcome component.
     *
     * @return Response The Inertia response with languages data
     */
    public function index(): Response
    {
        try {
            $languages = Language::all();

            return inertia('Welcome', [
                'languages' => $languages,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Database connection failed in index method: '.$e->getMessage());

            // Return user-friendly error message
            return inertia('Welcome', [
                'languages' => [],
                'error' => 'Unable to load programming languages. Please check your internet connection and try again.',
            ]);
        }
    }

    /**
     * Process the language selection and start the test.
     *
     * Validates the candidate's form submission including name, email, and selected
     * programming languages. Clears any existing test session data to prevent
     * conflicts and stores the candidate information in the session for use
     * throughout the assessment process.
     *
     * @param  Request  $request  The HTTP request containing candidate data
     * @return RedirectResponse Redirect to the test display page
     *
     * @throws \Illuminate\Validation\ValidationException When validation fails
     */
    public function startTest(Request $request): RedirectResponse
    {
        // Validate: Ensure 'languages' is present, is an array, and has at least 1 item.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'languages' => 'required|array|min:1',
        ]);

        // Clear any existing test session data
        session()->forget(['test_progress', 'test_start_time', 'test_question_ids']);

        // Session: Store the IDs so we remember them on the next page.
        session([
            'candidate_name' => $request->name,
            'candidate_email' => $request->email,
            'selected_languages' => $request->languages,
        ]);

        // Redirect: Go to the quiz display (we will create this route next).
        return redirect()->route('test.show');
    }

    /**
     * Display the assessment test interface.
     *
     * Retrieves questions based on selected languages, manages timer state to prevent
     * cheating, handles partial progress restoration, and prepares all necessary data
     * for the Vue.js Assessment component. Implements anti-cheat measures by tracking
     * test start time server-side and calculating remaining time based on elapsed time.
     *
     * @return Response|RedirectResponse Inertia response with test data or redirect if invalid
     */
    public function showTest()
    {
        try {
            // Retrieve the IDs we saved in the session
            $selectedIds = session('selected_languages', []);

            // If the session is empty (e.g. user goes directly to /assessment), redirect back
            if (empty($selectedIds)) {
                return redirect()->route('home')->with('error', 'Please select programming languages to start the assessment.');
            }

            // Fetch questions belonging to those languages
            // We use with('language') so we know which language each question belongs to
            $questions = \App\Models\Question::with('language')
                ->whereIn('language_id', $selectedIds)
                ->inRandomOrder()
                ->get();

            if ($questions->isEmpty()) {
                return redirect()->route('home')->with('error', 'No questions available for the selected programming languages. Please try again later.');
            }

            // Store question IDs in session for analytics calculation
            session(['test_question_ids' => $questions->pluck('id')->toArray()]);

            // Get saved progress from session if exists
            $savedProgress = session('test_progress', []);

            // Handle timer state - if no timer start time exists, set it now
            $testStartTime = session('test_start_time');
            $testDuration = 300; // 5 minutes in seconds

            if (! $testStartTime) {
                $testStartTime = now()->timestamp;
                session(['test_start_time' => $testStartTime]);
            }

            // Calculate remaining time based on elapsed time
            $elapsedTime = now()->timestamp - $testStartTime;
            $timeRemaining = max(0, $testDuration - $elapsedTime);

            // If time has expired, redirect to submit
            if ($timeRemaining <= 0) {
                return redirect()->route('test.submit-expired');
            }

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

            // Pass the questions and remaining time to Inertia
            return inertia('Assessment', [
                'questionsData' => $questionsData,
                'candidateName' => session('candidate_name'),
                'candidateEmail' => session('candidate_email'),
                'timeRemaining' => $timeRemaining,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Database error in showTest method: '.$e->getMessage());

            return redirect()->route('home')->with('error', 'Unable to load assessment questions. Please check your connection and try again.');
        }
    }

    /**
     * Process test submission and calculate results.
     *
     * Handles the final test submission by processing all answers, calculating
     * the score, generating detailed analytics, and storing the assessment record
     * in the database. Prepares comprehensive review data including question-by-question
     * analysis for the result page.
     *
     * @param  Request  $request  The HTTP request containing user answers
     * @return RedirectResponse Redirect to the result page
     */
    public function submitTest(Request $request): RedirectResponse
    {
        try {
            // Get user answers from the request
            $userAnswers = $request->input('answers', []); // format: [question_id => selected_option]

            // Get all question IDs from the test (including unanswered ones)
            $allQuestionIds = session('test_question_ids', []);

            if (empty($allQuestionIds)) {
                return redirect()->route('home')->with('error', 'Assessment session expired. Please start a new assessment.');
            }

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

            // Clear test session data including timer
            session()->forget(['test_progress', 'test_start_time', 'test_question_ids']);

            return redirect()->route('test.result');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Database error in submitTest method: '.$e->getMessage());

            return redirect()->route('home')->with('error', 'Unable to save your assessment results. Please try again or contact support.');
        }
    }

    /**
     * Handle automatic test submission when time expires.
     *
     * Called when the assessment timer reaches zero. Automatically submits
     * the test with whatever answers were previously saved in the session,
     * ensuring no candidate can extend their time beyond the limit.
     *
     * @return RedirectResponse Redirect to the result page via submitTest
     */
    public function submitExpiredTest(): RedirectResponse
    {
        // Get saved progress from session
        $savedProgress = session('test_progress', []);

        // Submit the test with whatever answers were saved
        return $this->submitTest(new Request(['answers' => $savedProgress]));
    }

    /**
     * Display the assessment results page.
     *
     * Shows the candidate's final score, detailed analytics, and conditional
     * features based on performance. If the score meets the threshold (50%),
     * enables resume upload functionality. Includes comprehensive question
     * review data for candidate learning.
     *
     * @return Response|RedirectResponse Inertia response with results or redirect if no session
     */
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

        return inertia('Result', [
            'score' => $score,
            'passed' => $passed,
            'assessmentId' => $assessmentId,
            'analytics' => $analytics,
            'allQuestionsReview' => $allQuestionsReview,
            'candidateName' => session('candidate_name'),
        ]);
    }

    /**
     * Handle resume file upload for successful candidates.
     *
     * Processes resume uploads for candidates who passed the assessment.
     * Validates file type (PDF, DOC, DOCX), size (max 2MB), and associates
     * the uploaded file with the specific assessment record. Implements
     * secure file storage and proper error handling.
     *
     * @param  Request  $request  The HTTP request containing the resume file
     * @return RedirectResponse Back to previous page with success/error message
     *
     * @throws \Illuminate\Validation\ValidationException When file validation fails
     */
    public function uploadResume(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'assessment_id' => 'required|exists:assessments,id',
            ], [
                'resume.required' => 'Please select a resume file to upload.',
                'resume.file' => 'The uploaded file is not valid.',
                'resume.mimes' => 'Resume must be a PDF, DOC, or DOCX file.',
                'resume.max' => 'Resume file size must not exceed 2MB.',
                'assessment_id.required' => 'Assessment ID is missing.',
                'assessment_id.exists' => 'Invalid assessment record.',
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

                return back()->with('success', 'Resume uploaded successfully! Your application is now complete.');
            }

            return back()->with('error', 'File upload failed. Please try again.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions to show specific error messages
            throw $e;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Resume upload error: '.$e->getMessage());

            // Check for specific error types
            if (strpos($e->getMessage(), 'disk space') !== false) {
                return back()->with('error', 'Server storage is full. Please try again later or contact support.');
            }

            if (strpos($e->getMessage(), 'permission') !== false) {
                return back()->with('error', 'Server permission error. Please contact support.');
            }

            return back()->with('error', 'Resume upload failed due to a server error. Please try again or contact support.');
        }
    }

    /**
     * Save candidate's progress during the assessment.
     *
     * Stores the candidate's current answers in the session to enable
     * progress restoration if they navigate away or experience connection
     * issues. Called automatically when answers are selected or navigation
     * occurs during the test.
     *
     * @param  Request  $request  The HTTP request containing current answers
     * @return \Illuminate\Http\JsonResponse JSON response confirming save status
     */
    public function saveProgress(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Validate that we have a valid test session
            $selectedLanguages = session('selected_languages');
            $testStartTime = session('test_start_time');
            
            if (empty($selectedLanguages) || !$testStartTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid test session. Please restart the assessment.',
                ], 400);
            }

            // Handle both JSON and FormData (from sendBeacon)
            $answers = [];
            if ($request->hasHeader('Content-Type') && str_contains($request->header('Content-Type'), 'application/json')) {
                $answers = $request->input('answers', []);
            } else {
                // Handle FormData from sendBeacon
                $answersJson = $request->input('answers');
                if ($answersJson) {
                    $answers = json_decode($answersJson, true) ?: [];
                }
            }

            // Validate answers format
            if (!is_array($answers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid answers format.',
                ], 400);
            }

            // Save progress to session
            session(['test_progress' => $answers]);

            return response()->json([
                'success' => true, 
                'message' => 'Progress saved',
                'saved_count' => count($answers)
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Progress save error: '.$e->getMessage(), [
                'request_data' => $request->all(),
                'session_data' => [
                    'has_languages' => !empty(session('selected_languages')),
                    'has_start_time' => !empty(session('test_start_time')),
                ]
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save progress. Your answers may not be preserved if you navigate away.',
            ], 500);
        }
    }
}
