<?php

namespace App\Services;

use App\Config\AssessmentConfig;
use App\ValueObjects\AssessmentResult;
use App\ValueObjects\QuestionAnalysis;
use Illuminate\Support\Collection;

/**
 * ScoringService
 *
 * Handles all scoring and analytics calculations for assessments.
 * Provides detailed analysis of candidate performance including
 * question-by-question breakdown and statistical metrics.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
class ScoringService
{
    /**
     * Calculate assessment score and generate detailed analytics.
     *
     * @param Collection $questions
     * @param array $userAnswers
     * @return AssessmentResult
     */
    public function calculateScore(Collection $questions, array $userAnswers): AssessmentResult
    {
        $totalQuestions = $questions->count();
        $correctCount = 0;
        $incorrectCount = 0;
        $skippedCount = 0;
        $questionAnalyses = [];

        foreach ($questions as $index => $question) {
            $submittedAnswer = $userAnswers[$question->id] ?? null;
            $analysis = $this->analyzeQuestion($question, $submittedAnswer, $index + 1);
            
            $questionAnalyses[] = $analysis;
            
            switch ($analysis->getStatus()) {
                case 'correct':
                    $correctCount++;
                    break;
                case 'incorrect':
                    $incorrectCount++;
                    break;
                case 'skipped':
                    $skippedCount++;
                    break;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        $analytics = [
            'total_questions' => $totalQuestions,
            'correct' => $correctCount,
            'incorrect' => $incorrectCount,
            'skipped' => $skippedCount,
            'answered' => $correctCount + $incorrectCount,
        ];

        return new AssessmentResult(
            $score,
            $analytics,
            $questionAnalyses
        );
    }

    /**
     * Analyze individual question performance.
     *
     * @param mixed $question
     * @param string|null $submittedAnswer
     * @param int $questionNumber
     * @return QuestionAnalysis
     */
    private function analyzeQuestion($question, ?string $submittedAnswer, int $questionNumber): QuestionAnalysis
    {
        $status = 'skipped';

        if ($submittedAnswer === null || $submittedAnswer === '') {
            $status = 'skipped';
        } elseif ($submittedAnswer === $question->correct_answer) {
            $status = 'correct';
        } else {
            $status = 'incorrect';
        }

        return new QuestionAnalysis(
            $questionNumber,
            $question->question_text,
            $question->language->name,
            $submittedAnswer,
            $question->correct_answer,
            $question->options,
            $status
        );
    }

    /**
     * Check if candidate passed based on score threshold.
     *
     * @param int $score
     * @param int|null $threshold
     * @return bool
     */
    public function hasPassed(int $score, ?int $threshold = null): bool
    {
        $threshold = $threshold ?? AssessmentConfig::getPassingThreshold();
        return $score >= $threshold;
    }
}