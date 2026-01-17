<?php

namespace App\ValueObjects;

/**
 * TestSession Value Object
 *
 * Immutable value object representing a candidate's test session.
 * Encapsulates all session-related data and provides methods for
 * calculating remaining time and validating session state.
 *
 * @author Laravel Assessment System
 * @version 1.0.0
 */
readonly class TestSession
{
    public function __construct(
        private string $candidateName,
        private string $candidateEmail,
        private array $selectedLanguages,
        private int $startTime,
        private int $duration
    ) {}

    public function getCandidateName(): string
    {
        return $this->candidateName;
    }

    public function getCandidateEmail(): string
    {
        return $this->candidateEmail;
    }

    public function getSelectedLanguages(): array
    {
        return $this->selectedLanguages;
    }

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Calculate remaining time in seconds.
     *
     * @return int
     */
    public function getRemainingTime(): int
    {
        $elapsedTime = now()->timestamp - $this->startTime;
        return max(0, $this->duration - $elapsedTime);
    }

    /**
     * Check if the session is still valid (not expired).
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->getRemainingTime() > 0;
    }

    /**
     * Get elapsed time in seconds.
     *
     * @return int
     */
    public function getElapsedTime(): int
    {
        return now()->timestamp - $this->startTime;
    }
}