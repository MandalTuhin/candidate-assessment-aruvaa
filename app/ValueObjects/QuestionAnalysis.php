<?php

namespace App\ValueObjects;

/**
 * QuestionAnalysis Value Object
 *
 * Immutable value object representing the analysis of a single question
 * in an assessment, including the candidate's answer, correct answer,
 * and performance status.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
readonly class QuestionAnalysis
{
    public function __construct(
        private int $questionNumber,
        private string $questionText,
        private string $languageName,
        private ?string $userAnswer,
        private string $correctAnswer,
        private array $options,
        private string $status
    ) {}

    public function getQuestionNumber(): int
    {
        return $this->questionNumber;
    }

    public function getQuestionText(): string
    {
        return $this->questionText;
    }

    public function getLanguageName(): string
    {
        return $this->languageName;
    }

    public function getUserAnswer(): ?string
    {
        return $this->userAnswer;
    }

    public function getCorrectAnswer(): string
    {
        return $this->correctAnswer;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Check if the answer was correct.
     *
     * @return bool
     */
    public function isCorrect(): bool
    {
        return $this->status === 'correct';
    }

    /**
     * Check if the question was skipped.
     *
     * @return bool
     */
    public function isSkipped(): bool
    {
        return $this->status === 'skipped';
    }

    /**
     * Convert to array for frontend consumption.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'question_number' => $this->questionNumber,
            'question_text' => $this->questionText,
            'language_name' => $this->languageName,
            'user_answer' => $this->userAnswer,
            'correct_answer' => $this->correctAnswer,
            'options' => $this->options,
            'status' => $this->status,
        ];
    }
}