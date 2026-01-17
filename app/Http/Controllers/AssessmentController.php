<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartTestRequest;
use App\Http\Requests\UploadResumeRequest;
use App\Models\Language;
use App\Services\AssessmentService;
use App\Services\FileUploadService;
use App\Services\SessionService;
use App\Services\ScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

/**
 * AssessmentController
 *
 * Handles HTTP requests for the technical assessment system.
 * Delegates business logic to appropriate services while managing
 * HTTP concerns like validation, redirects, and response formatting.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class AssessmentController extends Controller
{
    public function __construct(
        private AssessmentService $assessmentService,
        private SessionService $sessionService,
        private ScoringService $scoringService,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Display the language selection page.
     */
    public function index(): Response
    {
        try {
            $languages = Language::all();

            return inertia('Welcome', [
                'languages' => $languages,
            ]);
        } catch (\Exception $e) {
            Log::error('Database connection failed in index method: '.$e->getMessage());

            return inertia('Welcome', [
                'languages' => [],
                'error' => 'Unable to load programming languages. Please check your internet connection and try again.',
            ]);
        }
    }

    /**
     * Process the language selection and start the test.
     */
    public function startTest(StartTestRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->sessionService->initializeTestSession(
            $validated['name'],
            $validated['email'],
            $validated['languages']
        );

        return redirect()->route('test.show');
    }

    /**
     * Display the assessment test interface.
     */
    public function showTest(): Response|RedirectResponse
    {
        try {
            $testSession = $this->sessionService->getCurrentTestSession();

            if (!$testSession) {
                return redirect()->route('home')
                    ->with('error', 'Please select programming languages to start the assessment.');
            }

            if ($this->assessmentService->isTimeExpired($testSession)) {
                return redirect()->route('test.submit-expired');
            }

            $questions = $this->assessmentService->getQuestionsForAssessment(
                $testSession->getSelectedLanguages()
            );

            if ($questions->isEmpty()) {
                return redirect()->route('home')
                    ->with('error', 'No questions available for the selected programming languages. Please try again later.');
            }

            $this->sessionService->storeQuestionIds($questions->pluck('id')->toArray());
            $savedProgress = $this->sessionService->getSavedProgress();
            $questionsData = $this->assessmentService->prepareQuestionsData($questions, $savedProgress);

            return inertia('Assessment', [
                'questionsData' => $questionsData,
                'candidateName' => $testSession->getCandidateName(),
                'candidateEmail' => $testSession->getCandidateEmail(),
                'timeRemaining' => $testSession->getRemainingTime(),
            ]);
        } catch (\Exception $e) {
            Log::error('Database error in showTest method: '.$e->getMessage());

            return redirect()->route('home')
                ->with('error', 'Unable to load assessment questions. Please check your connection and try again.');
        }
    }

    /**
     * Process test submission and calculate results.
     */
    public function submitTest(Request $request): RedirectResponse
    {
        try {
            $testSession = $this->sessionService->getCurrentTestSession();
            
            if (!$testSession) {
                return redirect()->route('home')
                    ->with('error', 'Assessment session expired. Please start a new assessment.');
            }

            $userAnswers = $request->input('answers', []);
            $questionIds = $this->sessionService->getQuestionIds();

            if (empty($questionIds)) {
                return redirect()->route('home')
                    ->with('error', 'Assessment session expired. Please start a new assessment.');
            }

            $result = $this->assessmentService->processTestSubmission(
                $userAnswers,
                $questionIds,
                $testSession
            );

            $this->sessionService->storeAssessmentResults($result->toArray());
            $this->sessionService->clearTestSession();

            return redirect()->route('test.result');
        } catch (\Exception $e) {
            Log::error('Database error in submitTest method: '.$e->getMessage());

            return redirect()->route('home')
                ->with('error', 'Unable to save your assessment results. Please try again or contact support.');
        }
    }

    /**
     * Handle automatic test submission when time expires.
     */
    public function submitExpiredTest(): RedirectResponse
    {
        $savedProgress = $this->sessionService->getSavedProgress();
        return $this->submitTest(new Request(['answers' => $savedProgress]));
    }

    /**
     * Display the assessment results page.
     */
    public function showResult(): Response|RedirectResponse
    {
        $results = $this->sessionService->getAssessmentResults();

        if ($results['score'] === null) {
            return redirect()->route('home');
        }

        $passed = $this->scoringService->hasPassed($results['score']);

        return inertia('Result', [
            'score' => $results['score'],
            'passed' => $passed,
            'assessmentId' => $results['assessment_id'],
            'analytics' => $results['analytics'],
            'allQuestionsReview' => $results['all_questions_review'],
            'candidateName' => $results['candidate_name'],
        ]);
    }

    /**
     * Handle resume file upload for successful candidates.
     */
    public function uploadResume(UploadResumeRequest $request): RedirectResponse
    {
        try {
            Log::info('Resume upload started', [
                'request_data' => $request->all(),
                'files' => $request->allFiles()
            ]);
            
            $validated = $request->validated();
            
            Log::info('Validation passed', ['validated' => $validated]);

            $this->fileUploadService->uploadResume(
                $validated['resume'],
                $validated['assessment_id']
            );

            Log::info('Resume upload completed successfully');

            return back()->with('success', 'Resume uploaded successfully! Your application is now complete.');
        } catch (\InvalidArgumentException $e) {
            Log::error('Resume upload validation error', ['error' => $e->getMessage()]);
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Resume upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Resume upload failed due to a server error. Please try again or contact support.');
        }
    }

    /**
     * Save candidate's progress during the assessment.
     */
    public function saveProgress(Request $request): JsonResponse
    {
        try {
            if (!$this->sessionService->hasValidTestSession()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid test session. Please restart the assessment.',
                ], 400);
            }

            $answers = $this->extractAnswersFromRequest($request);

            if (!is_array($answers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid answers format.',
                ], 400);
            }

            $this->sessionService->saveProgress($answers);

            return response()->json([
                'success' => true,
                'message' => 'Progress saved',
                'saved_count' => count($answers)
            ]);
        } catch (\Exception $e) {
            Log::error('Progress save error: '.$e->getMessage(), [
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save progress. Your answers may not be preserved if you navigate away.',
            ], 500);
        }
    }

    /**
     * Extract answers from request handling both JSON and FormData.
     */
    private function extractAnswersFromRequest(Request $request): array
    {
        if ($request->hasHeader('Content-Type') && str_contains($request->header('Content-Type'), 'application/json')) {
            return $request->input('answers', []);
        }

        // Handle FormData from sendBeacon
        $answersJson = $request->input('answers');
        if ($answersJson) {
            return json_decode($answersJson, true) ?: [];
        }

        return [];
    }
}