<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Question;
use App\Repositories\QuestionRepositoryInterface;
use App\ValueObjects\AssessmentResult;
use App\ValueObjects\TestSession;
use Illuminate\Support\Collection;

/**
 * AssessmentService
 *
 * Core business logic service for handling technical assessments.
 * Encapsulates all assessment-related operations including question
 * retrieval, scoring, analytics calculation, and result processing.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class AssessmentService
{
    private const QUESTIONS_PER_LANGUAGE = 10;

    public function __construct(
        private QuestionRepositoryInterface $questionRepository,
        private SessionService $sessionService,
        private ScoringService $scoringService
    ) {}

    /**
     * Retrieve questions for the assessment based on selected languages.
     *
     * @param array $languageIds Selected programming language IDs
     * @return Collection<Question>
     */
    public function getQuestionsForAssessment(array $languageIds): Collection
    {
        // Calculate total limit: 10 questions per selected language
        $totalLimit = count($languageIds) * self::QUESTIONS_PER_LANGUAGE;
        
        return $this->questionRepository->getRandomQuestionsByLanguages($languageIds, $totalLimit);
    }

    /**
     * Prepare questions data for frontend consumption.
     *
     * @param Collection $questions
     * @param array $savedProgress
     * @return array
     */
    public function prepareQuestionsData(Collection $questions, array $savedProgress = []): array
    {
        return $questions->map(function ($question) use ($savedProgress) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'options' => $question->options,
                'correct_answer' => $question->correct_answer,
                'language_name' => $question->language->name,
                'selectedAnswer' => $savedProgress[$question->id] ?? null,
            ];
        })->values()->toArray();
    }

    /**
     * Process test submission and calculate results.
     *
     * @param array $userAnswers
     * @param array $questionIds
     * @param TestSession $testSession
     * @return AssessmentResult
     */
    public function processTestSubmission(
        array $userAnswers,
        array $questionIds,
        TestSession $testSession
    ): AssessmentResult {
        $questions = $this->questionRepository->getQuestionsByIds($questionIds);
        
        $result = $this->scoringService->calculateScore($questions, $userAnswers);
        
        // Save assessment to database
        $assessment = Assessment::create([
            'candidate_name' => $testSession->getCandidateName(),
            'candidate_email' => $testSession->getCandidateEmail(),
            'score' => $result->getScore(),
        ]);

        return $result->withAssessmentId($assessment->id);
    }

    /**
     * Check if assessment time has expired.
     *
     * @param TestSession $testSession
     * @return bool
     */
    public function isTimeExpired(TestSession $testSession): bool
    {
        return $testSession->getRemainingTime() <= 0;
    }
}