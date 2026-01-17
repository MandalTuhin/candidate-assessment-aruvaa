<?php

namespace App\ValueObjects;

/**
 * AssessmentResult Value Object
 *
 * Immutable value object representing the complete result of an assessment.
 * Contains score, analytics, and detailed question analysis data.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
readonly class AssessmentResult
{
    public function __construct(
        private int $score,
        private array $analytics,
        private array $questionAnalyses,
        private ?int $assessmentId = null
    ) {}

    public function getScore(): int
    {
        return $this->score;
    }

    public function getAnalytics(): array
    {
        return $this->analytics;
    }

    public function getQuestionAnalyses(): array
    {
        return $this->questionAnalyses;
    }

    public function getAssessmentId(): ?int
    {
        return $this->assessmentId;
    }

    /**
     * Create a new instance with assessment ID.
     *
     * @param int $assessmentId
     * @return self
     */
    public function withAssessmentId(int $assessmentId): self
    {
        return new self(
            $this->score,
            $this->analytics,
            $this->questionAnalyses,
            $assessmentId
        );
    }

    /**
     * Convert to array for session storage.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'assessment_id' => $this->assessmentId,
            'analytics' => $this->analytics,
            'all_questions_review' => array_map(
                fn(QuestionAnalysis $analysis) => $analysis->toArray(),
                $this->questionAnalyses
            ),
        ];
    }
}